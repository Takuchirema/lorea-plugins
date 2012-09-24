<?php

class FederatedComment {
	public static function create($params, $entity, $tag) {
		global $CONFIG;
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$parent_guid = $notification->getParentGUID();

		$comment_text = @current($entry->xpath("$tag/atom:content"));
		$parent = get_entity($parent_guid);

		$annotation = create_annotation($parent_guid,
                                                                'generic_comment',
                                                                $comment_text,
                                                                "",
                                                                $owner->guid,
                                                                $parent->access_id);
		$id = add_to_river('river/annotation/generic_comment/create', 'comment', $owner->guid, $parent->guid, "", 0, $annotation);
		AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);

		
		return $annotation;
	}
	public static function onCreateComment($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$id = $notification->getID();
		$river_id = AtomRiverMapper::getRiverID($id);

		if ($river_id || $notification->isLocal()) {
			return;
		}

		$author = FederatedObject::create($author, 'atom:author');

		if ($target) {
			$target['entry'] = $entry;
			$target['notification'] = $notification;
			$container = FederatedObject::create($target, 'activity:target');
			$object['container_entity'] = $container;
		}

		$object['owner_entity'] = $author;
		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$note = FederatedObject::create($object, 'activity:object');
	}


}

