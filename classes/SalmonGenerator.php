<?php

class SalmonGenerator
{	
	static function annotationToRiver($annotation, $verb, $view) {
			$item = new ElggRiverItem(new stdClass());
			$item->object_guid = $annotation->entity_guid;
			$item->subject_guid = $annotation->owner_guid;
			$item->action_type = $verb;
			$item->access_id = ACCESS_PUBLIC;
			$item->posted = $annotation->time_created;
			$item->annotation_id = $annotation->id;
			$item->view = $view;
			$item->id = 'comment-'.$annotation->id.'-'.$verb;
			return $item;
	}

	static function relationToRiver($relationship, $verb, $view) {
			$item = new ElggRiverItem(new stdClass());
			$item->object_guid = $relationship->guid_two;
			$item->subject_guid = $relationship->guid_one;
			$item->action_type = $verb;
			$item->access_id = ACCESS_PUBLIC;
			$item->posted = $relationship->time_created;
			$item->annotation_id = ACCESS_PUBLIC;
			$item->view = $view;
			$item->id = 'rel-'.$relationship->id.'-'.$verb;
			return $item;
	}
	static function relationToSalmon($relationship, $verb, $view) {
		$subject = get_entity($relationship->guid_one);
		$object = get_entity($relationship->guid_two);
		$relation_name = $relationship->relationship;

		if ($object->foreign && !$subject->foreign) {
			$item = SalmonGenerator::relationToRiver($relationship, $verb, $view);
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($object);
			SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
		}

	}
	static function annotationToSalmon($annotation, $verb, $view) {
		$subject = get_entity($annotation->owner_guid);
		$object = get_entity($annotation->entity_guid);
		$owner = get_entity($object->owner_guid);
		$annotation_name = $annotation->name;

		if ($object->foreign && !$subject->foreign) {
			$item = SalmonGenerator::annotationToRiver($annotation, $verb, $view);
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($owner);
			SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
		}

	}

	static function onPostComment($event, $object_type, $annotation) {
		if ($annotation->name == 'generic_comment') {
			SalmonGenerator::annotationToSalmon($annotation, 'comment', 'river/annotation/generic_comment/create');
		}
	}
	static function onActionCreate($event, $object_type, $relationship) {
		SalmonGenerator::relationToSalmon($relationship, 'join', 'river/relationship/member/create');
	}
	static function onActionDelete($event, $object_type, $relationship) {
		SalmonGenerator::relationToSalmon($relationship, 'leave', 'river/relationship/member/create');
	}
	static function onFollowCreate($event, $object_type, $relationship) {
		if ($relationship->relationship == 'follow') {
			// create friend request
			SalmonGenerator::relationToSalmon($relationship, 'follow', 'river/relationship/friend/create');
		}
	}

	static function onFollowDelete($event, $object_type, $relationship) {
		if ($relationship->relationship == 'follow') {
			// create friend request
			SalmonGenerator::relationToSalmon($relationship, 'unfollow', 'river/relationship/friend/create');
		}
	}

	static function onFriendCreate($event, $object_type, $relationship) {
		if ($relationship->relationship == 'friendrequest') {
			// create friend request
			SalmonGenerator::relationToSalmon($relationship, 'request-friend', 'river/relationship/friend/create');
		}
	}
	static function onFriendDelete($event, $object_type, $relationship) {
		if ($relationship->relationship == 'friendrequest') {
			$subject = get_entity($relationship->guid_one);
			$object = get_entity($relationship->guid_two);
			$friends = $object->isFriendsWith($subject->guid);
			if ($friends) {
				// accept friend request
				SalmonGenerator::relationToSalmon($relationship, 'friend', 'river/relationship/friend/create');
			} else {
				// decline friend request
				SalmonGenerator::relationToSalmon($relationship, 'decline-friend', 'river/relationship/friend/create');
			}
		}
		elseif ($relationship->relationship == 'friend') {
			$subject = get_entity($relationship->guid_one);
			$object = get_entity($relationship->guid_two);
			// delete friendship
			SalmonGenerator::relationToSalmon($relationship, 'remove-friend', 'river/relationship/friend/create');
		}
	}
	static function onRiverUpdate($event, $object_type, $item) {
		$subject_guid = $item->subject_guid;

		$access_id = $item->access_id;

		$object = ActivityStreams::getObject($item);
		$object_guid = $item->guid;
		$subject = get_entity($subject_guid);

		$hub_url = elgg_get_plugin_setting('hub', 'elgg-push');

		$container = get_entity($object->container_guid);
		$item_id = $item->id;

		// ensure only public stuff gets notified away (for now..)
		$action_type = $item->action_type;

		if ($object->access_id != ACCESS_PUBLIC || $subject->access_id != ACCESS_PUBLIC)
		        return $returnvalue;

		// object creation
		if (in_array($item->action_type, array("create", "reply")) && in_array($object->getSubtype(), array('groupforumtopic', 'topicreply', 'bookmarks', 'event_calendar', 'file'))) {
			$container = get_entity($object->container_guid);
			if ($container->foreign && !$object->foreign) {
				$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($container);
				SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
			}
		}

		return $returnvalue; // XXX check and enable one by one

		// check action types to see what to do
		if (($item->action_type == "create" || $item->action_type == "update") && in_array($object->getSubtype(), array("blog", "bookmarks", 'groupforumpost', 'groupforumtopic', 'page', 'page_top', 'tasks', 'event_calendar'))) {
			$container = get_entity($object->container_guid);
			if ($container->foreign && !$object->foreign) {
				$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($container);
				SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
			}
		}
		elseif (($item->action_type == "done" || $item->action_type == "undone") && in_array($object->getSubtype(), array("tasks"))) {
			$container = get_entity($object->container_guid);
			if ($container->foreign && !$object->foreign) {
				$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($container);
				SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
			}
		}
		return $returnvalue;
	}
}
