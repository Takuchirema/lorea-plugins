<?php

class FederatedBookmark {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;

		// specific fields
		$description = @current($entry->xpath("$tag/atom:summary"));
		$address = @current($entry->xpath("$tag/atom:link[attribute::rel='related']/@href"));

		if (empty($entity)) {
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
			if ($params['tags'])
				$entity->tags = $params['tags'];

			// atom fields
			$entity->atom_id = $params['id'];
			$entity->atom_link = $params['link'];
			$entity->foreign = true;
			if ($entity->save()) {
				if ($notification->getVerb() == 'post') {
   			        	$id = add_to_river('river/object/bookmarks/create','create', $owner->getGUID(), $entity->getGUID());
					AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
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

