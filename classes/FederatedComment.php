<?php

class FederatedComment {
	public static function create($params, $entity, $tag) {
		global $CONFIG;
		$owner = $params['owner_entity'];
		login($owner);
		$entry = $params['entry'];
		$notification = $params['notification'];

		$parent = $notification->getParent();
		if ($parent instanceof ElggEntity) {
			$parent_guid = $parent->getGUID();
		}
		else {
			// annotation
			$parent = get_entity($parent->entity_guid);
			$parent_guid = $parent->getGUID();
		}

		$comment_text = $notification->xpath(array("$tag/atom:content", "atom:content"));
		$parent = get_entity($parent_guid);

		// comments of thewire become thewire messages too
		if ($parent->getSubtype() == 'thewire') {
			return FederatedNote::create($params, $entity, $tag);
		}

		$annotation = create_annotation($parent_guid,
                                                                'generic_comment',
                                                                $comment_text,
                                                                "",
                                                                $owner->guid,
                                                                $parent->access_id);

		AtomRiverMapper::setAnnotationIDMapping($annotation, $params['id']);

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
		$note = FederatedObject::create($object, 'activity:object', false);
	}


}

