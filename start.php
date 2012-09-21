<?php

function federated_menu_item($entity) {
	$options = array(
                'name' => 'federated',
                'text' => elgg_echo('federated_objects_federated'),
                'href' => false,
                'priority' => 1000,
        );
        return ElggMenuItem::factory($options);

}

function federated_river_menu_setup($hook, $type, $return, $params) {
	$item = $params['item'];
	$object = $item->getObjectEntity();

	if ($object->foreign) {	
		$return[] = federated_menu_item($object);
	}

	return $return;
}

function federated_entity_menu_setup($hook, $type, $return, $params) {
        if (elgg_in_context('widgets')) {
                return $return;
	$entity = $params['entity'];


	if ($entity->foreign) {	
		$return[] = federated_menu_item($entity);
	}

	return $return;
}


function federated_objects_init() {
	// menu hooks
	elgg_register_plugin_hook_handler('register', 'menu:river', 'federated_river_menu_setup', 400);
        elgg_register_plugin_hook_handler('register', 'menu:entity', 'federated_entity_menu_setup', 400);

	// callback for incoming items for now coming from pubsubhub
	elgg_register_plugin_hook_handler('push:notification', 'atom', array('FederatedNotification', 'notification'));

	// callbacks for specific actions, implement procedures to manage object - verb combinations
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postLogger'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'group', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', array('FederatedGroup', 'onGroupJoin'));

	// override atom id for foreign objects
	elgg_register_plugin_hook_handler('activitystreams:id', 'entity', array('AtomRiverMapper', 'entity_id'));
	elgg_register_plugin_hook_handler('activitystreams:id', 'river', array('AtomRiverMapper', 'river_id'));

	// object constructors, plugins can register their own to support new data types
	FederatedObject::register_constructor('person', array('FederatedPerson', 'create'));
	FederatedObject::register_constructor('note', array('FederatedNote', 'create'));
	FederatedObject::register_constructor('bookmark', array('FederatedBookmark', 'create'));
	FederatedObject::register_constructor('group', array('FederatedGroup', 'create'));

	// override object urls
	if (is_plugin_enabled('profile')) {
		elgg_register_entity_url_handler('user', 'all', array('FederatedPerson', 'url'));
	}
	if (is_plugin_enabled('thewire')) {
		elgg_register_entity_url_handler('object', 'thewire', array('FederatedNote', 'url'));
	}
	if (is_plugin_enabled('bookmarks')) {
		elgg_register_entity_url_handler('object', 'bookmarks', array('FederatedBookmark', 'url'));
	}
	#if (is_plugin_enabled('groups')) {
#		elgg_register_entity_url_handler('group', 'all', array('FederatedGroup', 'url'));
#	}
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
