<?php
/**
* Elgg microthemes plugin
* This plugin lets users send messages to each other.
*
* @package ElggMicrothemes
*/


elgg_register_event_handler('init', 'system', 'microthemes_init');

function microthemes_init() {

	// register a library of helper functions
	elgg_register_library('elgg:microthemes', elgg_get_plugins_path() . 'microthemes/lib/microthemes.php');

	// add page menu items
	elgg_register_event_handler('pagesetup', 'system', 'microthemes_pagesetup');
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'microthemes_user_hover_menu');

	// Extend system CSS with our own styles, which are defined in the microthemes/css view
	elgg_extend_view('css/elgg', 'microthemes/css');
	elgg_extend_view('js/elgg', 'microthemes/js');
	
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('microthemes', 'microthemes_page_handler');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'microthemes/actions/microthemes';
	elgg_register_action("microthemes/edit", "$action_path/edit.php");
	elgg_register_action("microthemes/delete", "$action_path/delete.php");
	elgg_register_action("microthemes/choose", "$action_path/choose.php");
	elgg_register_action("microthemes/clear", "$action_path/clear.php");
}

/**
 * Messages page handler
 *
 * @param array $page Array of URL components for routing
 * @return bool
 */
function microthemes_page_handler($page) {

	elgg_set_context('microthemes');
	
	$base_dir = elgg_get_plugins_path() . 'microthemes/pages/microthemes';
	
	switch ($page[0]) {
		case 'css':
			include("$base_dir/css.php");
			break;
		case 'edit':
			set_input('guid', $page[1]);
			include("$base_dir/edit.php");
			break;
		case 'view':
			set_input('assign_to', $page[1]);
			include("$base_dir/view.php");
			break;
		default:
			return false;
	}
	return true;
}

function microthemes_pagesetup() {
	$owner = elgg_get_page_owner_entity();
	if ((elgg_instanceof($owner, 'user') || elgg_instanceof($owner, 'group'))
														&& $owner->canEdit()) {
		elgg_register_menu_item('page', array(
			'name' => 'choose_profile_microtheme',
			'href' => "microthemes/view/" . $owner->username,
			'text' => elgg_echo('microthemes:profile:edit'),
			'contexts' => array('profile_edit'),
		));
		elgg_register_menu_item('page', array(
			'name' => 'microthemes',
			'text' => elgg_echo('microthemes:group:edit'),
			'href' => "microthemes/view/" . $owner->guid,
			'context' => array('groups'),
		));
	}
}

function microthemes_user_hover_menu($hook, $type, $return, $params) {
	$user = $params['entity'];
	if (elgg_is_logged_in() && elgg_get_logged_in_user_guid() == $user->guid) {
		$url = "microthemes/$user->username/edit";
		$item = new ElggMenuItem('microthemes:profile:edit', elgg_echo('microthemes:profile:edit'), $url);
		$item->setSection('action');
		$return[] = $item;
	}
	return $return;
}
