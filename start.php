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

	// notifications
	register_notification_object('object', 'assembly', elgg_echo('assemblies:newpost'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'assemblies_notify_message');

	// add group assemblies link to
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'assemblies_owner_block_menu');

	// Add group option
	add_group_tool_option('assemblies', elgg_echo('assemblies:enableassemblies'), false);
	#elgg_extend_view('groups/tool_latest', 'assemblies/group_module');
	elgg_extend_view('groups/profile/summary','assemblies/group_module');

	// add a assemblies widget
	elgg_register_widget_type('assemblies', elgg_echo('assemblies'), elgg_echo('assemblies:widget:description'));

	// register actions
	//$action_path = elgg_get_plugins_path() . 'assemblies/actions/assemblies';
	//elgg_register_action('assemblies/save', "$action_path/save.php");
	//elgg_register_action('assemblies/delete', "$action_path/delete.php");

	// entity menu
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'assemblies_entity_menu_setup');

	// ecml
	elgg_register_plugin_hook_handler('get_views', 'ecml', 'assemblies_ecml_views_hook');

	// specific actions
	$action_path = elgg_get_plugins_path() . 'assemblies/actions/assemblies';
	elgg_register_action("assemblies/general", "$action_path/general.php");
	// data types
	elgg_set_config('assembly', array(
		#'title' => 'text',
		#'description' => 'longtext',
		'date' => 'date',
		'location' => 'text',
		#'tags' => 'tags',
		'access_id' => 'access',
	));
	
	if (elgg_is_active_plugin('crud')) {
		$crud = crud_register_type('assembly');
		$crud->children_type = 'agenda_point';
		$crud->module = 'assemblies';
		$crud->list_order = 'date';
		$crud->list_order_direction = 'DESC';
	}

	elgg_set_config('agenda_point', array(
		'title' => 'text',
		'description' => 'longtext',
		#'date' => 'date',
		'status' => 'crudselect',
		'mode' => 'crudselect',
		'tags' => 'tags',
		'access_id' => 'access',
	));
	
	if (elgg_is_active_plugin('crud')) {
		$crud = crud_register_type('agenda_point');
		#$crud->children_type = 'agenda_point';
		$crud->module = 'assemblies';
		$crud->icon_var = 'status';
		$crud->setVariable('status', 'crudselect', array('new', 'accepted', 'discarded', 'delayed'), 'new');
		$crud->setVariable('mode', 'crudselect', array('permanent', 'conjunctural'), 'conjunctural');
		$crud->list_tabs = 'mode';
	}

}

/**
 * Add a menu item to an ownerblock
 */
function assemblies_owner_block_menu($hook, $type, $return, $params) {
	if (elgg_instanceof($params['entity'], 'group')) {
		if ($params['entity']->assemblies_enable == "yes") {
			$url = "assembly/owner/{$params['entity']->guid}";
			$item = new ElggMenuItem('assemblies', elgg_echo('assemblies:assembly:group'), $url);
			$return[] = $item;
			$url = "agenda_point/owner/{$params['entity']->guid}";
			$item = new ElggMenuItem('agenda_points', elgg_echo('assemblies:agenda_point:group'), $url);
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

