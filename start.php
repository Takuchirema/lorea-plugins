<?php
/**
 * Online plugin
 * 
 * This is a rewrite of the Bogdan Nikovskiy's Online plugin,
 * written in 2009.
 *
 */

elgg_register_event_handler('init', 'system', 'online_init');

function online_init() {

	elgg_extend_view('css/elgg', 'online/css');
	elgg_extend_view('css/admin', 'online/css');
	
	elgg_register_plugin_hook_handler('find_active_users', 'system', 'online_find_active_friends');
}

function online_find_active_friends($hook, $type, $return, $params) {
	
	if (elgg_is_admin_logged_in()) {
		return $return;
	}
	
	// Else, show only online friends
	
	global $CONFIG;

	$time = time() - $params['seconds'];
	$logged_in_user_guid = elgg_get_logged_in_user_guid();

	$data = elgg_get_entities(array(
		'type' => 'user', 
		'limit' => $params['limit'],
		'offset' => $params['offset'],
		'count' => $params['count'],
		'joins' => array(
						"join {$CONFIG->dbprefix}users_entity u on e.guid = u.guid",
						"join {$CONFIG->dbprefix}entity_relationships r on e.guid = r.guid_one"
					),
		'wheres' => array(
						"u.last_action >= {$time}",
						"r.relationship = 'friend'",
						"r.guid_two = $logged_in_user_guid", 
					),
		'order_by' => "u.last_action desc"
	));
	
	return $data;
}
