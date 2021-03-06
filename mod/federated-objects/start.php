<?php
/**
 * Federated Objects -- Create objects from OStatus and Salmon remote sources.
 *
 * @package        Lorea
 * @subpackage     FederatedObjects
 * @homepage       https://lorea.org/plugin/federated-objects
 * @copyright      2012-2013 Lorea Faeries <federation@lorea.org>
 * @license        COPYING, http://www.gnu.org/licenses/agpl
 *
 * Copyright 2012-2013 Lorea Faeries <federation@lorea.org>
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

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
	elgg_register_plugin_hook_handler('federated_objects:post', 'event', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'file', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', array('FederatedNotification', 'postObjectCreator'));
	elgg_register_plugin_hook_handler('federated_objects:post', 'page', array('FederatedNotification', 'postObjectCreator'));
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
	FederatedObject::register_constructor('event', array('FederatedEvent', 'create'));
	FederatedObject::register_constructor('file', array('FederatedFile', 'create'));
	FederatedObject::register_constructor('article', array('FederatedArticle', 'create'));
	FederatedObject::register_constructor('page', array('FederatedPage', 'create'));

	// page handler
	elgg_register_page_handler('federated-objects','federated_objects_page_handler');

	// override object urls
	if (elgg_is_active_plugin('profile')) {
		elgg_register_entity_url_handler('user', 'all', array('FederatedPerson', 'url'));
	}
	if (elgg_is_active_plugin('thewire')) {
		elgg_register_entity_url_handler('object', 'thewire', array('FederatedNote', 'url'));
	}
	if (elgg_is_active_plugin('bookmarks')) {
		elgg_register_entity_url_handler('object', 'bookmarks', array('FederatedBookmark', 'url'));
	}
	if (elgg_is_active_plugin('groups')) {
		elgg_register_entity_url_handler('group', 'all', array('FederatedGroup', 'url'));
	}
	if (elgg_is_active_plugin('threads')) {
		elgg_register_entity_url_handler('object', 'groupforumtopic', array('FederatedThread', 'url'));
	}

	if (elgg_is_active_plugin('event_calendar')) {
		elgg_register_entity_url_handler('object', 'event_calendar', array('FederatedEvent', 'url'));
	}

	if (elgg_is_active_plugin('file')) {
		elgg_register_entity_url_handler('object', 'file', array('FederatedFile', 'url'));
	}

	if (elgg_is_active_plugin('blog')) {
		elgg_register_entity_url_handler('object', 'blog', array('FederatedArticle', 'url'));
	}

	if (elgg_is_active_plugin('pages')) {
		elgg_register_entity_url_handler('object', 'page', array('FederatedPage', 'url'));
		elgg_register_entity_url_handler('object', 'page_top', array('FederatedPage', 'url'));
	}


	// add provenance to river items
	elgg_extend_view('river/item', 'federated-objects/item');
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
