<?php
/**
 * Assemblies
 *
 * @package Assemblies
 *
 */

elgg_register_event_handler('init', 'system', 'assemblies_init');

/**
 * Init assemblies plugin.
 */
function assemblies_init() {

	elgg_register_library('elgg:assemblies', elgg_get_plugins_path() . 'assemblies/lib/assemblies.php');

	// add to the main css
	elgg_extend_view('css/elgg', 'assemblies/css');

	// routing of urls
	elgg_register_page_handler('assembly', 'assemblies_page_handler');

	// override the default url to view a assembly object
	elgg_register_entity_url_handler('object', 'assembly', 'assembly_url_handler');

	// notifications
	register_notification_object('object', 'assembly', elgg_echo('assemblies:newpost'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'assemblies_notify_message');

	// add group assemblies link to
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'assemblies_owner_block_menu');

	// Register for search.
	elgg_register_entity_type('object', 'assembly');

	// Add group option
	add_group_tool_option('assemblies', elgg_echo('assemblies:enableassemblies'), true);
	elgg_extend_view('groups/tool_latest', 'assemblies/group_module');

	// add a assemblies widget
	elgg_register_widget_type('assemblies', elgg_echo('assemblies'), elgg_echo('assemblies:widget:description'));

	// register actions
	$action_path = elgg_get_plugins_path() . 'assemblies/actions/assemblies';
	elgg_register_action('assemblies/edit', "$action_path/edit.php");
	elgg_register_action('assemblies/delete', "$action_path/delete.php");

	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'assemblies_entity_menu_setup');

	// ecml
	elgg_register_plugin_hook_handler('get_views', 'ecml', 'assemblies_ecml_views_hook');
}

/**
 * Assemblies page handler
 *
 * URLs take the form of
 *  All assemblies in site:     assembly/all
 *  List assemblies in group:   assembly/owner/<guid>
 *  View assembly:              assembly/view/<guid>
 *  Add assembly call:          assembly/add/<guid>
 *  Edit assembly call/minutes: assembly/edit/<guid>
 *
 * @param array $page Array of url segments for routing
 * @return bool
 */
function assemblies_page_handler($page) {

	elgg_load_library('elgg:assemblies');

	if (!isset($page[0])) {
		$page[0] = 'all';
	}

	elgg_push_breadcrumb(elgg_echo('assemblies'), 'assemblies/all');

	switch ($page[0]) {
		case 'all':
			assemblies_handle_all_page();
			break;
		case 'owner':
			assemblies_handle_list_page($page[1]);
			break;
		case 'add':
			assemblies_handle_edit_page('add', $page[1]);
			break;
		case 'edit':
			assemblies_handle_edit_page('edit', $page[1]);
			break;
		case 'view':
			assemblies_handle_view_page($page[1]);
			break;
		default:
			return false;
	}
	return true;
}

/**
 * Format and return the URL for assemblies.
 *
 * @param ElggObject $entity Assembly object
 * @return string URL of assembly.
 */
function assemblies_url_handler($entity) {
	if (!$entity->getOwnerEntity()) {
		// default to a standard view if no owner.
		return FALSE;
	}

	$friendly_title = elgg_get_friendly_title($entity->title);

	return "assembly/view/{$entity->guid}/$friendly_title";
}

/**
 * Add a menu item to an ownerblock
 */
function assemblies_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->assemblies_enable != "no") {
			$url = "assemblies/owner/{$params['entity']->guid}";
			$item = new ElggMenuItem('assemblies', elgg_echo('assemblies:group'), $url);
			$return[] = $item;
		}
	}

	return $return;
}

/**
 * Add particular assembly links/info to entity menu
 */
function assemblies_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}

	$entity = $params['entity'];
	$handler = elgg_extract('handler', $params, false);
	if ($handler != 'assembly') {
		return $return;
	}

	return $return;
}

/**
 * Set the notification message body
 * 
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the assembly posted
 * @return string
 */
function assembly_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'assembly')) {
		$descr = $entity->excerpt;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('assembly:notification', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}

/**
 * Register assemblies with ECML.
 */
function assemblies_ecml_views_hook($hook, $entity_type, $return_value, $params) {
	$return_value['object/assembly'] = elgg_echo('assemblies:assemblies');

	return $return_value;
}
