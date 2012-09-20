<?php

function federated_objects_init() {
	// callback for incoming items for now coming from pubsubhub
	elgg_register_plugin_hook_handler('push:notification', 'atom', array('FederatedNotification', 'notification'));

	// callbacks for specific actions, implement procedures to manage object - verb combinations
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', array('FederatedNotification', 'postLogger'));

	// override atom id for foreign objects
	elgg_register_plugin_hook_handler('activitystreams:id', 'entity', array('FederatedNotification', 'entity_id'));
	elgg_register_plugin_hook_handler('activitystreams:id', 'river', array('FederatedNotification', 'river_id'));

	// object constructors, plugins can register their own to support new data types
	FederatedObject::register_constructor('person', array('FederatedPerson', 'create'));
	FederatedObject::register_constructor('note', array('FederatedNote', 'create'));

	// override object urls
	if (is_plugin_enabled('profile')) {
		elgg_register_entity_url_handler('user', 'all', array('FederatedPerson', 'url'));
	}
	if (is_plugin_enabled('thewire')) {
		elgg_register_entity_url_handler('object', 'thewire', array('FederatedNote', 'url'));
	}
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
