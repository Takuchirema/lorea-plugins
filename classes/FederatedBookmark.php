<?php

class FederatedBookmark {
	public static function create($params, $entity) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;

		// specific fields
		$description = @current($entry->xpath("activity:object/atom:summary"));
		$address = @current($entry->xpath("activity:object/atom:link[attribute::rel='related']/@href"));

		if ($entity) {
			$note = $entity;
		}
		else {
			$access = elgg_set_ignore_access(true);

			$entity = new ElggObject;
			// regular fields
			$entity->owner_guid = $owner->getGUID();
			$entity->access_id = $access_id;
			$entity->subtype = "bookmarks";
			if (isset($params['container_entity'])) {
				$entity->container_guid = $params['container_entity']->getGUID();
			}
			$entity->title = $params['name'];
			$entity->description = $description;
			$entity->address = $address;

			// atom fields
			$entity->atom_id = $params['id'];
			$entity->atom_link = $params['link'];
			$entity->foreign = true;
			if ($entity->save()) {
				if ($notification->getVerb() == 'create') {
   			             $id = add_to_river('river/object/bookmarks/create','create', $owner->getGUID(), $entity->getGUID());
				     FederatedNotification::setIDMapping($id, $notification->getID());
			        }
			}

			elgg_set_ignore_access($access);
		}
		return $entity;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return bookmark_url($object);
	}
}

