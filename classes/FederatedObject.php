<?php

global $FEDERATED_CONSTRUCTORS;
$FEDERATED_CONSTRUCTORS = array();

class FederatedObject {

	/**
	 * Load xml
	 */
	public static function find($webid) {
		$options = array('metadata_name' => 'atom_id',
				 'metadata_value' => $webid,
				 'owner_guid' => ELGG_ENTITIES_ANY_VALUE);
                $entities = elgg_get_entities_from_metadata($options);
                if ($entities) {
                        return $entities[0];
                }
	}

	public static function create($params) {
		global  $FEDERATED_CONSTRUCTORS;
		$type = $params['type'];
		$entity = FederatedObject::find($params['id']);
		if (isset($FEDERATED_CONSTRUCTORS[$type])) {
			return call_user_func($FEDERATED_CONSTRUCTORS[$type], $params, $entity);
		}
	}

	public static function isLocalID($webid) {
		// check if id starts with site url
		$site_url = elgg_get_site_url();
		if (strpos($webid, $site_url) === 0) {
			return true;
		}

		// check if id starts with tag:host,
		$host = parse_url($site_url, PHP_URL_HOST);
		if (strpos($webid, "tag:$host,") === 0) {
			return true;
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
				FederatedNotification::setIDMapping($river_item->id, $notification->getID());
			}
	}


}
