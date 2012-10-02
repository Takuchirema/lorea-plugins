<?php
	elgg_load_library('elgg:push');

	$user = get_loggedin_user();

	$endpoint = get_input("atom_endpoint");
	$salmon_link = get_input("salmon_endpoint");
	$webid = get_input("webid");
	$hub = get_input("hub");

	$subscription = push_get_subscription($endpoint);
	if ($subscription->status === 'subscribed') {
		$has_subscription = "yes";
	}
	else {
		push_subscribeto($endpoint);
		$has_subscription = "no";
	}

	$feed = OstatusProtocol::getFeed($webid);
	$author = $feed->getAuthor();

	$entity = FederatedObject::create($author, 'atom:author');
	$entity->salmon_link = $salmon_link;

	if ($entity instanceof ElggGroup) {
		// assume the group is public
		if (!check_entity_relationship($user->guid ,'member', $entity->guid)) {
			if (groups_join_group($entity, $user)) {
 //                               FederatedObject::search_tag_river($entity, $user, 'join', $notification);
                        }
		}
	}
	else {
		if (!check_entity_relationship($user->guid ,'follow', $entity->guid)) {
			add_entity_relationship($user->guid ,'follow', $entity->guid);
		}

	}

