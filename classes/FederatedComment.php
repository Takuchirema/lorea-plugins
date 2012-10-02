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
			if (!$parent) {
				error_log("cant find {$params['id']}");
				return;
			}
			$parent_guid = $parent->getGUID();
		}

		$comment_text = $notification->getBody();
		$parent = get_entity($parent_guid);

		// comments of thewire become thewire messages too
		if (in_array($parent->getSubtype(), array('thewire', 'groupforumtopic', 'topicreply'))) {
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
		$attention = $notification->getAttention();

		$id = $notification->getID();
		$river_id = AtomRiverMapper::getRiverID($id);

		if ($river_id || $notification->isLocal()) {
			return;
		}

		$author = FederatedObject::create($author, 'atom:author');

		if ($attention) {
			$object['container_entity'] = $notification->getAttentionGroup();
		}
		elseif ($target) {
			$container = FederatedObject::create($target, 'activity:target');
			$object['container_entity'] = $container;
		}

		$object['owner_entity'] = $author;
		$note = FederatedObject::create($object, 'activity:object', false);
	}


}

