<?php
/**
 * Federated Objects -- Federated Friends
 *
 * @package        Lorea
 * @subpackage     FederatedObjects
 *
 * Copyright 2012-2013 Lorea Faeries <federation@lorea.org>
 *
 * This file is part of the FederatedObjects plugin for Elgg.
 *
 * FederatedObjects is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Affero General Public License
 * as published by the Free Software Foundation, either version 3 of
 * the License, or (at your option) any later version.
 *
 * FederatedObjects is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public
 * License along with this program. If not, see
 * <http://www.gnu.org/licenses/>.
 */

class FederatedFriends {
	public static function onFriend($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$user = FederatedObject::create($author, 'atom:author');

		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$friend = FederatedObject::create($object, 'activity:object');

		login($user);

		FederatedFriends::addFriend($user, $friend, $notification);

		error_log("onFriend");
	}
	public static function onRemoveFriend($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$user = FederatedObject::create($author, 'atom:author');

		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$friend = FederatedObject::create($object, 'activity:object');

		login($user);


		if ($user->isFriendsWith($friend->guid)) {
			$user->removeFriend($friend->guid);
			if($friend->isFriendsWith($user->guid)) {
				$friend->removeFriend($user->guid);
			}
		}
		error_log("onDeleteFriend");
	}
	public static function onRequestFriend($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$user = FederatedObject::create($author, 'atom:author');

		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$friend = FederatedObject::create($object, 'activity:object');

		login($user);

		if (!FederatedFriends::addFriend($user, $friend, $notification)) {
			add_entity_relationship($user->guid, "friendrequest", $friend->guid);
		}
		error_log("onRequestFriend");
	}
	public static function onDeclineFriend($hook, $type, $return, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$user = FederatedObject::create($author, 'atom:author');

		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$friend = FederatedObject::create($object, 'activity:object');

		login($user);

		error_log("onDeclineFriend");
		remove_entity_relationship($friend->guid, 'friendrequest', $user->guid);
	}
	public static function addFriend($user, $friend, $notification) {
		if ($user->isFriendsWith($friend->guid) && $friend->isFriendsWith($user->guid)) {
			return; // already friends
		}
		if(check_entity_relationship($friend->guid, "friendrequest", $user->guid)
 		       || check_entity_relationship($friend->guid, "friend", $user->guid)) {

			$user->addFriend($friend->guid);

			$friend->addFriend($user->guid);
			remove_entity_relationship($friend->guid, "friendrequest", $user->guid);

			$id = add_to_river('river/relationship/friend/create', 'friend', $user->guid, $friend->guid);
			AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
			$id = add_to_river('river/relationship/friend/create', 'friend', $friend->guid, $user->guid);
			AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
			return true;
		}
		else {
			return false;
		}
	}
	public static function onFollow($hook, $type, $return, $params) {
		FederatedFriends::doRelationshipApply('follow', true, $params);
	}
	public static function onUnfollow($hook, $type, $return, $params) {
		FederatedFriends::doRelationshipApply('follow', false, $params);
	}
	public static function doRelationshipApply($relationship, $add, $params) {
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		$user = FederatedObject::create($author, 'atom:author');

		$friend = $params['target_entity'];

		if (empty($friend) || empty($user)) {
			if (empty($friend))
				error_log("onUnfollow no friend!");
			elseif (empty($user))
				error_log("onUnfollow no user!");
			return;
		}
		login($user);

		if ($add) {
			if (!check_entity_relationship($user->guid, $relationship, $friend->guid)) {
				add_entity_relationship($user->guid, $relationship, $friend->guid);
			}
		} else {
			if (check_entity_relationship($user->guid, $relationship, $friend->guid)) {
				remove_entity_relationship($user->guid, $relationship, $friend->guid);
			}

		}
	}
}

