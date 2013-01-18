<?php
/**
 * Federated Objects -- Federated Discussion Thread
 *
 * @package        Lorea
 * @subpackage     FederatedObjects
 *
 * Copyright 2012-2013 Lorea Faeries <federation@lorea.org>
 *
 * This file is part of the FederatedObjects plugin for Elgg.
 *
 * FederatedObjects is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * FederatedObjects is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

class FederatedThread {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;

		$body = $notification->getBody();

		if ($entity) {
			$topic = $entity;
		}
		else {
			$parent_guid = $notification->getParentGUID();

			$access = elgg_set_ignore_access(true);

			if ($parent_guid) {
				elgg_load_library('elgg:threads');
				$guid = threads_reply($parent_guid, $body, $params['name']);
				$topic = threads_top($guid);
				$id = add_to_river('river/annotation/group_topic_post/reply', 'reply', $owner->getGUID(), $topic->guid, "", 0, $guid);
				// now get the current message
				$topic = get_entity($guid);

			}
			else {
				$topic = new ElggObject();
				$topic->owner_guid = $owner->getGUID();
				$topic->subtype = 'groupforumtopic';
				$topic->title = $params['name'];
				$topic->description = $body;
				$topic->status = 'open';
				$topic->access_id = $access_id;

				if (isset($params['container_entity'])) {
					$topic->container_guid = $params['container_entity']->getGUID();
				}
				if ($params['tags']) {
					$topic->tags = $params['tags'];
				}
				$topic->save();
				$id = add_to_river('river/object/groupforumtopic/create', 'create', $owner->getGUID(), $topic->guid);
			}
			AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
			$topic->atom_id = $params['id'];
			$topic->atom_link = $params['link'];
			$topic->foreign = true;


			elgg_set_ignore_access($access);
		}
		return $topic;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return discussion_override_topic_url($object);
	}
}

