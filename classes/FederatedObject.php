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
		error_log("CREATE:".$FEDERATED_CONSTRUCTORS[$type].":".$params['id']);
		return call_user_func($FEDERATED_CONSTRUCTORS[$type], $params, $entity);
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

	public static function randomString($length)
	{
	    // Generate random 32 charecter string
	    $string = md5(time());

	    // Position Limiting
	    $highest_startpoint = 32-$length;

	    // Take a random starting point in the randomly
	    // Generated String, not going any higher then $highest_startpoint
	    $randomString = substr($string,rand(0,$highest_startpoint),$length);

	    return $randomString;

	}

	public static function create_person($params, $entity) {
		if ($entity) {
			error_log("federated_objects_create_person:exists!");
		}
		else {
			error_log("federated_objects_create_person:doesnt exists!". $params['id']);
			$access = elgg_set_ignore_access(true);
			$entity = new ElggUser();
			$entity->owner_guid = 0;
			$entity->container_guid = 0;
			$entity->subtype = 'ostatus';
			$entity->username = FederatedObject::randomString(8);
			$entity->save();
			$entity->username = 'ostatus_'.$entity->getGUID();
			$entity->name = $params['name'];
			$entity->access_id = ACCESS_PUBLIC;
			$entity->atom_id = $params['id'];
			$entity->foreign = true;
			$entity->save();
			elgg_set_ignore_access($access);
		}
		return $entity;
	}

	public static function create_note($params, $entity) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$access_id = ACCESS_PUBLIC;
		$method = 'ostatus';

		$body = @current($entry->xpath("activity:object/atom:content"));
		$body = elgg_strip_tags($body);
		if (empty($body)) {
			$body = $entry->xpath("atom:content");
			if (is_array($body))
				$body = @current($body);
			if ($body)
				$body = $body->asXML();
		}


		if ($entity) {
			$note = $entity;
		}
		else {
			$parent_id = @current($entry->xpath("activity:object/thr:in-reply-to/atom:id"));
			if ($parent_id) {
				$parent = FederatedObject::find($parent_id);
				$parent_guid = $parent->getGUID();
			}
			$access = elgg_set_ignore_access(true);

			$guid = thewire_save_post($body, $owner->getGUID(), $access_id, $parent_guid, $method);
			$note = get_entity($guid);
			$note->atom_id = $params['id'];
			$note->foreign = true;
			//add_to_river('river/object/thewire/create', 'create', $post->owner_guid, $post->guid);
			$options = array('object_guid'=>$guid, 'action_types'=>'create', 'subject_guid'=>$owner->getGUID());
			$river_items = elgg_get_river($options);
			if ($river_items) {
				$river_item = $river_items[0];
				FederatedNotification::setIDMapping($river_item->id, $params['id']);
			}

			elgg_set_ignore_access($access);
		}
		return $note;
}


}
