<?php

class FederatedPage {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;

		// specific fields
		$body = $notification->getBody();
		$write_access_id = ACCESS_PRIVATE;

		if (empty($entity)) {
			$access = elgg_set_ignore_access(true);
			$parent = $notification->getParent();

			$entity = new ElggObject;
			// regular fields
			$entity->owner_guid = $owner->getGUID();
			$entity->access_id = $access_id;
			if ($parent) {
				$entity->subtype = "page";
			}
			else {
				$entity->subtype = "page_top";
			}

			if (isset($params['container_entity'])) {
				$entity->container_guid = $params['container_entity']->getGUID();
			}
			$entity->title = $params['name'];
			$entity->description = $body;
			if ($params['tags'])
				$entity->tags = $params['tags'];

			// page fields
			$entity->write_access_id = $write_access_id;
			if ($parent) {
				$entity->parent_guid = $parent->getGUID();
			}

			// atom fields
			$entity->atom_id = $params['id'];
			$entity->atom_link = $params['link'];
			$entity->foreign = true;
			if ($entity->save()) {
				$entity->annotate('page', $entity->description, $entity->access_id);
				if ($notification->getVerb() == 'post') {
   			        	$id = add_to_river('river/object/page/create','create', $owner->getGUID(), $entity->getGUID());
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
		return pages_url($object);
	}
}

