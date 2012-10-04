<?php
/**
 * Elggman plugin
 *
 * @package Elggman
 */

elgg_register_event_handler('init', 'system', 'elggman_init');

function elggman_dummy($from, $to, $subject, $topic, $params = array()) {
}

/**
 * Elggman plugin initialization functions.
 */
function elggman_init() {

	// register a library of helper functions
	elgg_register_library('elggman', elgg_get_plugins_path() . 'elggman/lib/elggman.php');

	// Extend CSS
	elgg_extend_view('css/elgg', 'elggman/css');
	
	elgg_extend_view('groups/sidebar/members', 'elggman/sidebar/info');
	elgg_extend_view('discussion/sidebar', 'elggman/sidebar/info');

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('elggman', 'elggman_page_handler');
	
	elgg_register_event_handler('create', 'object', 'elggman_notifications');
	// threaded topicreply's
	elgg_register_event_handler('create', 'top', 'elggman_notifications');
	
	// Register granular notification for this object type
	//register_notification_object('object', 'groupforumtopic', elgg_echo('elggman:newupload'));

	// Listen to notification events and supply a more useful message
	//elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'file_notify_message');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'elggman/actions/elggman';
	elgg_register_action("elggman/subscribe", "$action_path/subscribe.php");
	elgg_register_action("elggman/unsubscribe", "$action_path/unsubscribe.php");
	elgg_register_action("elggman/subscription/edit", "$action_path/subscription/edit.php");

	$current_page = current_page_url();
	if (!strpos($current_page, 'notifications/personal')) {
		register_notification_handler('mailshot', 'elggman_dummy');
	}
}

/**
 * Dispatches subscription pages.
 * URLs take the form of
 *  User's subscriptions: elggman/owner/<username>
 *  View subscription:    elggman/view/<guid>/
 *  Edit subscription:    elggman/edit/<guid>
 *
 * @param array $page
 * @return bool
 */
function elggman_page_handler($page) {

	$pages_dir = elgg_get_plugins_path() . 'elggman/pages/elggman';

	$page_type = $page[0];
	switch ($page_type) {
		case 'owner':
			include "$pages_dir/owner.php";
			break;
		case 'view':
			set_input('guid', $page[1]);
			include "$pages_dir/view.php";
			break;
		case 'edit':
			set_input('guid', $page[1]);
			include "$pages_dir/edit.php";
			break;
		case 'receive':
			include "$pages_dir/receive.php";
			break;
		default:
			return false;
	}
	return true;
}

function elggman_send_email($from, $to, $subject, $body, $params) {
	 // return TRUE/FALSE to stop elgg_send_email() from sending
        $mail_params = array(
                                                        'to' => $to,
                                                        'from' => $from,
                                                        'subject' => $subject,
                                                        'body' => $body,
                                                        'params' => $params
                                        );

        $result = elgg_trigger_plugin_hook('email', 'system', $mail_params, NULL);
        if ($result !== NULL) {
                return $result;
        }

	$headers = $params['headers'];
	$body = preg_replace("/^From/", ">From", $body); // Change lines starting with From to >From

	// Sanitise subject by stripping line endings
        $subject = preg_replace("/(\r\n|\r|\n)/", " ", $subject);
        if (is_callable('mb_encode_mimeheader')) {
                $subject = mb_encode_mimeheader($subject, "UTF-8", "B");
        }

	return mail($to, $subject, wordwrap($body), $headers);
}

function elggman_notifications($event, $object_type, $object) {
	if ($object_type == 'top') {
		$object = get_entity($object->guid_one);
		$is_reply = true;
	}

	if (elgg_instanceof($object, 'object', 'groupforumtopic')
			|| (elgg_instanceof($object, 'object', 'topicreply') && $object_type == 'top')) {
		$user  = $object->getOwnerEntity();
		$group = $object->getContainerEntity();
		
		$from = elggman_get_user_email($user, $group);
		$mailing_list_email = elggman_get_group_mailinglist($group);
		if(!$mailing_list_email) {
			return;
		}
		
		elgg_load_library("elgg:threads");
		$parent = threads_parent($object->guid);
		$top = threads_top($object->guid);
		
		$subject = "[$group->name] $top->title";
		
		if ($is_reply) {
			$subject = "Re: $subject";
		}
		
		elgg_set_viewtype("email");
		$message = elgg_view('page/elements/body', array(
			'value' => $object->description,
			'post_url' => $object->getURL(),
			'mailing_list' => $group,
			));
			
		$headers = elgg_view('page/elements/header', array(
			'From' => $from,
			'To' => $mailing_list_email,
			'Sender' => $mailing_list_email,
			'Reply-To' => $mailing_list_email,
			'List-Id' => str_replace('@', '.', $mailing_list_email),
			'List-Post' => "<mailto:{$mailing_list_email}>",
			'Precedence' => "list",
			'Message-Id' => "{$object->guid}.{$mailing_list_email}",
			'In-Reply-To' => $is_reply ? "{$parent->guid}.{$mailing_list_email}" : false,
			));
		
		foreach (elggman_get_subscriptors($group->guid) as $subscriptor) {
			$to = $subscriptor->email;
			elggman_send_email($from, $to, $subject, $message, array('headers' => $headers));
		}
	}
}

function elggman_is_user_subscribed($user_guid, $group_guid) {
	return check_entity_relationship($user_guid, 'notifymailshot', $group_guid);
}

function elggman_get_subscriptors($group_guid) {
	return elgg_get_entities_from_relationship(array(
				'type' => 'user',
				'relationship' => 'notifymailshot',
				'relationship_guid' => $group_guid,
				'inverse_relationship' => TRUE,
				'limit' => 0,
				));
}

function elggman_apikey() {
	return sha1(get_site_secret().'elggman');
}

function elggman_get_user_email($user, $group) {
	if (check_entity_relationship($user->guid, 'obfuscated_groupmailshot', $group->guid)) {
		return $user->username . '@' . parse_url(elgg_get_site_url(), PHP_URL_HOST);
	} else {
		return $user->email;
	}
}

function elggman_get_group_mailinglist($group) {
	if ($group->alias) {
		return $group->alias . '@' . elgg_get_plugin_setting('mailname', 'elggman');
	}
	return false;
}
