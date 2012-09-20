<?php

function federated_objects_init() {

	// callback for incoming items for now coming from pubsubhub
	elgg_register_plugin_hook_handler('push:notification', 'atom', array('FederatedNotification', 'notification'));

	// callbacks for specific actions, implement procedures to manage object - verb combinations
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', array('FederatedNotification', 'postLogger'));

	elgg_register_plugin_hook_handler('activitystreams:id', 'entity', array('FederatedNotification', 'entity_id'));
	elgg_register_plugin_hook_handler('activitystreams:id', 'river', array('FederatedNotification', 'river_id'));

	// object constructors, plugins can register their own to support new data types
	FederatedObject::register_constructor('person', array('FederatedObject', 'create_person'));
	FederatedObject::register_constructor('note', array('FederatedObject', 'create_note'));
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
