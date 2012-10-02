<?php

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

