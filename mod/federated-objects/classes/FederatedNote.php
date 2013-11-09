<?php
/**
 * Federated Objects -- Federated Note
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

class FederatedNote {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;
		$method = 'ostatus';

		$body = strip_tags($notification->getBody());

		if (isset($params['container_entity'])) {
			// group notes become threads for now
			return FederatedThread::create($params, $entity, $tag);
		}

		if ($entity) {
			$note = $entity;
		}
		else {
			$parent_guid = $notification->getParentGUID();

			$access = elgg_set_ignore_access(true);

			$guid = thewire_save_post($body, $owner->getGUID(), $access_id, $parent_guid, $method);
			$note = get_entity($guid);
			$note->atom_id = $params['id'];
			$note->atom_link = $params['link'];
			$note->foreign = true;

			FederatedObject::search_tag_river($note, $owner, 'create', $notification);

			elgg_set_ignore_access($access);
		}
		return $note;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return thewire_url($object);
	}
}

