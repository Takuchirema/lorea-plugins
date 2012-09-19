<?php

function federated_objects_init() {
	#elgg_register_library('elgg:push', elgg_get_plugins_path() . 'elgg-push/lib/push.php');

	elgg_register_plugin_hook_handler('push:notification', 'atom', array('FederatedNotification', 'notification'));

	// callbacks for specific actions
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', array('FederatedNotification', 'postLogger'));
	FederatedObject::register_constructor('person', array('FederatedObject', 'create_person'));
	FederatedObject::register_constructor('note', array('FederatedObject', 'create_note'));
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
