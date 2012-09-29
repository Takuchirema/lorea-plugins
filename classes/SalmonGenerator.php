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
		$annotation_name = $annotation->name;

		if ($object->foreign && !$subject->foreign) {
			$item = SalmonGenerator::annotationToRiver($annotation, $verb, $view);
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($object);
			SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
		}

	}

	static function onPostComment($event, $object_type, $annotation) {
		SalmonGenerator::annotationToSalmon($annotation, 'comment', 'river/annotation/generic_comment/create');
	}
	static function onActionCreate($event, $object_type, $relationship) {
		SalmonGenerator::relationToSalmon($relationship, 'join', 'river/relationship/member/create');
	}
	static function onActionDelete($event, $object_type, $relationship) {
		SalmonGenerator::relationToSalmon($relationship, 'leave', 'river/relationship/member/create');
	}
	static function onFollowCreate($event, $object_type, $relationship) {
		$subject = get_entity($relationship->guid_one);
		$object = get_entity($relationship->guid_two);
		if ($relationship->relationship == 'follow') {
			// create friend request
			SalmonGenerator::relationToSalmon($relationship, 'follow', 'river/relationship/friend/create');
		}
	}

	static function onFollowDelete($event, $object_type, $relationship) {
		$subject = get_entity($relationship->guid_one);
		$object = get_entity($relationship->guid_two);
		if ($relationship->relationship == 'follow') {
			// create friend request
			SalmonGenerator::relationToSalmon($relationship, 'unfollow', 'river/relationship/friend/create');
		}
	}

	static function onFriendCreate($event, $object_type, $relationship) {
		$subject = get_entity($relationship->guid_one);
		$object = get_entity($relationship->guid_two);
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
		$object_guid = $item->object_guid;
		$subject_guid = $item->subject_guid;

		$access_id = $item->access_id;

		$object = get_entity($object_guid);
		$subject = get_entity($subject_guid);

		$hub_url = elgg_get_plugin_setting('hub', 'elgg-push');

		$container = get_entity($object->container_guid);
		$item_id = $item->id;

		// ensure only public stuff gets notified away (for now..)
		$action_type = $item->action_type;

		if ($object->access_id != ACCESS_PUBLIC || $subject->access_id != ACCESS_PUBLIC)
		        return $returnvalue;

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
		elseif ($subject instanceof ElggUser && $item->action_type == "comment" && ($object->foreign || $container->foreign)) {
			if ($object->foreign)
				$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($container); // XXXX
			else
				$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($container);
			SalmonProtocol::sendUpdate($salmon_link, $item, $object, $subject);
		}
		return $returnvalue;
	}

        function create_object($event, $object_type, $object) {
                if ($event == 'create' && $object->getSubtype() === 'messages') {
                        $fromId = $object->fromId;
                        $toId = $object->toId;
                        $fromEntity = get_entity($object->fromId);
                        $toEntity = get_entity($object->toId);
                        if ($toEntity->foreign && !$fromEntity->foreign && $fromEntity->guid == $object->owner_guid)
                        {

                        $viewtype = elgg_get_viewtype();
                        elgg_set_viewtype('atom');
                        $update = elgg_view('activitystreams/entry',
                                array('standalone'=>true,
                                        'entry_id'=>$object->getURL().$object->time_created,
                                        'verb'=>'sendto',
                                        'title'=>$object->title,
                                        'body'=>$object->description,
                                        'annotation_id'=>null,
                                        'created'=>$object->time_created,
                                        'updated'=>$object->time_created,
                                        'subject'=>$fromEntity,
                                        'container'=>$toEntity,
                                        'entity'=>$object));
                                elgg_set_viewtype($viewtype);
                                // need the salmon link here
                                $endpoint = SalmonDiscovery::getSalmonEndpointEntity($toEntity);
                                if ($endpoint)
                                        SalmonProtocol::postEnvelope($endpoint, $update, $fromEntity);
                        }
                }

        }
        function group_addtogroup($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::group_sendrequest('addtogroup');
                return $returnvalue;
        }
        function group_killrequest($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::group_sendrequest('killrequest');
                return $returnvalue;
        }
        function group_joinrequest($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::group_sendrequest('joinrequest');
                return $returnvalue;
        }
        function group_sendrequest($verb) {
                global $CONFIG;
                if (!$subject = get_entity(get_input('user_guid', get_loggedin_userid())))
                        return (false);
                if (!$object = get_entity(get_input('group_guid', 0)))
                        return (false);
                if (!($subject->foreign || $object->foreign))
                        return;
                $time = time();
                $viewtype = elgg_get_viewtype();
                elgg_set_viewtype('atom');
                $update = '<entry>'.elgg_view('activitystreams/entry',
                        array('standalone'=>true,
                                'entry_id'=>$CONFIG->wwwroot.$object->guid.$time,
                                'verb'=>$verb,
                                'title'=>$object->name,
                                'body'=>$object->name,
                                'annotation_id'=>null,
                                'created'=>$time,
                                'updated'=>$time,
                                'subject'=>$subject,
                                'container'=>$object,
                                'entity'=>$object)).'</entry>';
                elgg_set_viewtype($viewtype);
                if ($subject->atom_id) { // remote controlling
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($subject);
                        SalmonProtocol::sendUpdate($salmon_link, $update, $subject);
                }
                if ($object->foreign) {
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($object);
                        SalmonProtocol::sendUpdate($salmon_link, $update, $subject);
                }
        }
        function friend_sendrequest($verb, $inputpar) {
                global $CONFIG;
                if (!$friend = get_entity(get_input($inputpar, 0)))
                        return (false);
                if (!$friend->foreign)
                        return;
                $time = time();
                $user = get_loggedin_user();
                $viewtype = elgg_get_viewtype();
                elgg_set_viewtype('atom');
                $update = elgg_view('activitystreams/entry',
                        array('standalone'=>true,
                                'entry_id'=>$CONFIG->wwwroot.$friend->username.$time,
                                'verb'=>$verb,
                                'title'=>$friend->name,
                                'body'=>$friend->name,
                                'annotation_id'=>null,
                                'created'=>$time,
                                'updated'=>$time,
                                'subject'=>$user,
                                'container'=>$friend,
                                'entity'=>$friend));
                elgg_set_viewtype($viewtype);
                if ($user->atom_id) {
			$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($user);
                        SalmonProtocol::sendUpdate($salmon_link, $update, $user);
                }
		$salmon_link = SalmonDiscovery::getSalmonEndpointEntity($friend);
                SalmonProtocol::sendUpdate($salmon_link, $update, $user);
        }
        function add_friend($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::friend_sendrequest('requestfriendship', 'friend');
                return $returnvalue;
        }
        function approve_friend($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::friend_sendrequest('approvefriendship', 'guid');
                return $returnvalue;
        }
        function decline_friend($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::friend_sendrequest('declinefriendship', 'guid');
                return $returnvalue;
        }
        function remove_friend($hook, $entity_type, $returnvalue, $params) {
                SalmonGenerator::friend_sendrequest('removefriendship', 'friend');
                return $returnvalue;
        }
        function flag_user($hook, $entity_type, $returnvalue, $params) {
                $entity = get_entity(get_input('uid'));
                if (!($entity->type == 'user' && $entity->foreign))
                        return;
                SalmonGenerator::friend_sendrequest('http://activitystrea.ms/schema/1.0/follow', 'uid');
                return $returnvalue;
        }
        function unflag_user($hook, $entity_type, $returnvalue, $params) {
                if (!($entity->type == 'user' && $entity->foreign))
                        return;
                SalmonGenerator::friend_sendrequest('http://ostatus.org/schema/1.0/unfollow', 'uid');
                return $returnvalue;
        }


}
