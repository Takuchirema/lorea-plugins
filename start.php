<?php

function federated_menu_item($entity, $name='federated') {
	if ($entity->foreign) {
		$url = elgg_get_site_url() . "federated-objects/" . $entity->guid;
	}
	else {
		$url = false;
	}
	$options = array(
                'name' => $name,
                'text' => elgg_echo("federated_objects:$name"),
                'href' => $url,
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
	$provenance = AtomRiverMapper::getRiverProvenance($item->id);
	if (!empty($provenance)) {
		$return[] = federated_menu_item($object, "provenance");
	}

	return $return;
}

function federated_entity_menu_setup($hook, $type, $return, $params) {
        if (elgg_in_context('widgets')) {
                return $return;
	}
	$entity = $params['entity'];


	if ($entity->foreign) {	
		$return[] = federated_menu_item($entity);
	}

	return $return;
}

function federated_objects_page_handler($page) {
	$guid = (int)$page[0];
	$entity = get_entity($guid);
	if ($entity->foreign) {
		echo elgg_view_page('', elgg_view('federated-objects/view',
				     array('entity' => $entity)));
	} else {
		forward();
	}
}

function federated_objects_init() {
	// menu hooks
	elgg_register_plugin_hook_handler('register', 'menu:river', 'federated_river_menu_setup', 400);
        elgg_register_plugin_hook_handler('register', 'menu:entity', 'federated_entity_menu_setup', 400);

	// callback for incoming items for now coming from pubsubhub
	elgg_register_plugin_hook_handler('push:notification', 'atom', array('FederatedNotification', 'notification'));

	// callbacks for specific actions, implement procedures to manage object - verb combinations
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postLogger'));
	// ObjectCreation
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'comment', array('FederatedComment', 'onCreateComment'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'group', array('FederatedNotification', 'postObjectCreator'));
	// Groups
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', array('FederatedGroup', 'onGroupJoin'));
	elgg_register_plugin_hook_handler('federated_objects:leave', 'group', array('FederatedGroup', 'onGroupLeave'));
	// Friends
	elgg_register_plugin_hook_handler('federated_objects:friend', 'person', array('FederatedFriends', 'onFriend'));
	elgg_register_plugin_hook_handler('federated_objects:remove-friend', 'person', array('FederatedFriends', 'onRemoveFriend'));
	elgg_register_plugin_hook_handler('federated_objects:request-friend', 'person', array('FederatedFriends', 'onRequestFriend'));
	elgg_register_plugin_hook_handler('federated_objects:decline-friend', 'person', array('FederatedFriends', 'onDeclineFriend'));
	elgg_register_plugin_hook_handler('federated_objects:follow', 'person', array('FederatedFriends', 'onFollow'));
	elgg_register_plugin_hook_handler('federated_objects:unfollow', 'person', array('FederatedFriends', 'onUnfollow'));

	// override atom id for foreign objects
	elgg_register_plugin_hook_handler('activitystreams:id', 'entity', array('AtomRiverMapper', 'entity_id'));
	elgg_register_plugin_hook_handler('activitystreams:id', 'river', array('AtomRiverMapper', 'river_id'));
	elgg_register_plugin_hook_handler('activitystreams:id', 'annotation', array('AtomRiverMapper', 'annotation_id'));

	// object constructors, plugins can register their own to support new data types
	FederatedObject::register_constructor('person', array('FederatedPerson', 'create'));
	FederatedObject::register_constructor('note', array('FederatedNote', 'create'));
	FederatedObject::register_constructor('bookmark', array('FederatedBookmark', 'create'));
	FederatedObject::register_constructor('group', array('FederatedGroup', 'create'));
	FederatedObject::register_constructor('comment', array('FederatedComment', 'create'));

	// page handler
	elgg_register_page_handler('federated-objects','federated_objects_page_handler');

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
	if (is_plugin_enabled('groups')) {
		elgg_register_entity_url_handler('group', 'all', array('FederatedGroup', 'url'));
	}

	// add provenance to river items
	elgg_extend_view('river/item', 'federated-objects/item');
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
