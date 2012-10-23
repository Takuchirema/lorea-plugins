<?php
/**
 * Elgg GnuPG plugin
 *
 * @package ElggPG
 */

elgg_register_event_handler('init', 'system', 'elggpg_init');

/**
 * GnuPG plugin initialization functions.
 */
function elggpg_init() {
	
	// Register library
	elgg_register_library('elggpg', elgg_get_plugins_path() . 'elggpg/lib/elggpg.php');
	elgg_register_library('elggpg:send:override', elgg_get_plugins_path() . 'elggpg/lib/messages_send.php');
	
	// Extend CSS
	elgg_extend_view('css/elgg', 'elggpg/css');
	
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('elggpg', 'elggpg_page_handler');
	
	// Register entity type for search
	elgg_register_entity_type('object', 'elggpg');

	// Register a notification handler to encrypt messages
	elgg_register_plugin_hook_handler('email', 'system', 'elggpg_send_email_handler');
	
	elgg_extend_view("core/settings/account/email", "elggpg/viewkey", 1);
	
	// Actions
	$actions_path = elgg_get_plugins_path() . 'elggpg/actions/elggpg';
	elgg_register_action("elggpg/pubkey_upload", "$actions_path/pubkey_upload.php");
	elgg_register_action("elggpg/pubkey_delete", "$actions_path/pubkey_delete.php");
	elgg_register_action("messages/send", "$actions_path/send_encrypted.php");

	// add a GPG link to owner blocks
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'elggpg_owner_block_menu');

	// upgrade
	elgg_register_event_handler('upgrade', 'system', 'elggpg_run_upgrades');

}

/**
 * Page handler
*/
function elggpg_page_handler($page) {

	$pages_dir = elgg_get_plugins_path() . 'elggpg/pages/elggpg';
	switch($page[0]) {
		case 'owner':
			include("$pages_dir/owner.php");
			break;
		case 'raw':
			set_input('username', $page[1]);
			include("$pages_dir/raw.php");
			break;
		case 'download':
			set_input('username', $page[1]);
			include("$pages_dir/download.php");
			break;
		default:
			return false;
	}
	return true;
}

function elggpg_send_email_handler($hook, $type, $return, $params) {
	$from = $params['from'];
	$to = $params['to'];
	$subject = $params['subject'];
	$body = $params['body'];
	$headers = $params['headers'];
	
	$receiver = current(get_user_by_email($to));
	
	// Format message
	$body = html_entity_decode($body, ENT_COMPAT, 'UTF-8'); // Decode any html entities
	$body = elgg_strip_tags($body); // Strip tags from message
	$body = preg_replace("/(\r\n|\r)/", "\n", $body); // Convert to unix line endings in body
	$body = preg_replace("/^From/", ">From", $body); // Change lines starting with From to >From
	$body = wordwrap($body);
	
	// Encrypting
	if (elgg_get_plugin_user_setting('encrypt_emails', $receiver->guid, 'elggpg') != 'no') {
		elgg_load_library('elggpg');
		$encrypted_body = elggpg_encrypt($body, $receiver, false);
		if ($encrypted_body) {
			$body = $encrypted_body;
		}
	}

	// The following code is the same that in elgg's
	
	$header_eol = "\r\n";
	if (isset($CONFIG->broken_mta) && $CONFIG->broken_mta) {
		// Allow non-RFC 2822 mail headers to support some broken MTAs
		$header_eol = "\n";
	}

	// Windows is somewhat broken, so we use just address for to and from
	if (strtolower(substr(PHP_OS, 0, 3)) == 'win') {
		// strip name from to and from
		if (strpos($to, '<')) {
			preg_match('/<(.*)>/', $to, $matches);
			$to = $matches[1];
		}
		if (strpos($from, '<')) {
			preg_match('/<(.*)>/', $from, $matches);
			$from = $matches[1];
		}
	}

	if (empty($headers)) {
		$headers = "From: $from{$header_eol}"
			. "Content-Type: text/plain; charset=UTF-8; format=flowed{$header_eol}"
			. "MIME-Version: 1.0{$header_eol}"
			. "Content-Transfer-Encoding: 8bit{$header_eol}";
	}

	// Sanitise subject by stripping line endings
	$subject = preg_replace("/(\r\n|\r|\n)/", " ", $subject);
	if (is_callable('mb_encode_mimeheader')) {
		$subject = mb_encode_mimeheader($subject, "UTF-8", "B");
	}

	return mail($to, $subject, $body, $headers);
}

/**
 * Add a menu item to an ownerblock
 * 
 * @param string $hook
 * @param string $type
 * @param array  $return
 * @param array  $params
 */
function elggpg_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'user') && ($params['entity']->isFriend() || $params['entity']->guid == elgg_get_logged_in_user_guid())) {
		$url = "elggpg/owner/{$params['entity']->username}";
		if ($params['entity'] == elgg_get_logged_in_user_entity()) {
			$item = new ElggMenuItem('elggpg', elgg_echo('elggpg:manage'), $url);
		} else {
			$item = new ElggMenuItem('elggpg', elgg_echo('elggpg:view'), $url);
		}
		$item->setSection('manage');
		$return[] = $item;
	}
	return $return;
}

/**
 * Process upgrades for the elggpg plugin
 */
function elggpg_run_upgrades() {
	$path = elgg_get_plugins_path() . 'elggpg/upgrades/';
	$files = elgg_get_upgrade_files($path);
	foreach ($files as $file) {
		include "$path{$file}";
	}
}
