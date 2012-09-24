<?php

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

}

