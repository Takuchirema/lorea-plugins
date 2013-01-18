<?php
/**
 * Federated Objects -- Generic Federated Object Class
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

global $FEDERATED_CONSTRUCTORS;
$FEDERATED_CONSTRUCTORS = array();

class FederatedObject {

	/**
	 * Load xml
	 */
	public static function find($webid) {
		if (FederatedObject::isLocalID($webid)) {
			return FederatedObject::findLocal($webid);
		}
		else {
			return FederatedObject::findRemote($webid);
		}
	}
	public static function findLocal($webid) {
		if (strpos($webid, '@') !== FALSE) {
			$parts = explode('@', $webid);
			$entity_id = $parts[0];
		} else {
			$parts = explode('/', $webid);
			if (strpos($webid, "/annotation/" !== FALSE)) {
				return elgg_get_annotation_from_id($parts[4]);
			}
			$entity_id = $parts[2];
		}
		if (is_numeric($entity_id)) {
			error_log("FINMD ENTITY $entity_id");
			return get_entity($entity_id);
		} else {
			error_log("FINMD ENTITY $entity_id");
			return get_user_by_username($entity_id);
		}
	}
	public static function findRemote($webid) {
		// XXX missing finding remote annotations
		$options = array('metadata_name' => 'atom_id',
				 'metadata_value' => $webid,
				 'owner_guid' => ELGG_ENTITIES_ANY_VALUE);
                $entities = elgg_get_entities_from_metadata($options);
                if ($entities) {
                        return $entities[0];
                }
		// try to find an annotation if entity failed
		$id = AtomRiverMapper::getAnnotationID($webid);
		if ($id) {
			return elgg_get_annotation_from_id($id);
		}
	}

	public static function create($params, $tag, $find=true) {
		global  $FEDERATED_CONSTRUCTORS;
		$type = $params['type'];
		if ($find)
			$entity = FederatedObject::find($params['id']);
		if (isset($FEDERATED_CONSTRUCTORS[$type])) {
			return call_user_func($FEDERATED_CONSTRUCTORS[$type], $params, $entity, $tag);
		}
	}

	public static function isLocalID($webid) {
		// check if id starts with site url
		$site_url = elgg_get_site_url();
		if (strpos($webid, $site_url) === 0) {
			return true;
		}

		$host = parse_url($site_url, PHP_URL_HOST);
		if (strpos($webid, '@') !== FALSE) {
			$parts = explode('@', $webid);
			if ($host == $parts[1]) {
				return true;
			}
		}
		else {
			// check if id starts with tag:host,
			if (strpos($webid, "tag:$host,") === 0) {
				return true;
			}
		}
		return false;
	}

	public static function register_constructor($type, $callback) {
		global  $FEDERATED_CONSTRUCTORS;
		$FEDERATED_CONSTRUCTORS[$type] = $callback;
	}

	public static function search_tag_river($object, $owner, $action, $notification) {
			$options = array('object_guid' => $object->getGUID(),
					 'action_types' => $action,
					 'subject_guid' => $owner->getGUID());
			$river_items = elgg_get_river($options);
			if ($river_items) {
				$river_item = $river_items[0];
				AtomRiverMapper::setIDMapping($river_item->id, $notification->getID(), $notification->provenance);
			}
	}


}
