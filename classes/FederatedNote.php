<?php

class FederatedNote {
	public static function create($params, $entity) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;
		$method = 'ostatus';

		$body = $notification->getBody();

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

