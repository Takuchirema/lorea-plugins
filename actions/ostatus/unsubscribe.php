<?php
	elgg_load_library('elgg:push');

	$user = get_loggedin_user();

	$endpoint = get_input("atom_endpoint");
	$salmon_link = get_input("salmon_endpoint");
	$webid = get_input("webid");
	$hub = get_input("hub");

	if (FederatedObject::isLocalID($webid)) {
		$entity = FederatedObject::find($webid);
		$is_local = true;
	}
	else {
		$feed = OstatusProtocol::getFeed($webid);
		$author = $feed->getAuthor();

		$entity = FederatedObject::create($author, 'atom:author');
		$entity->salmon_link = $salmon_link;
		$is_local = false;
	}


	if ($entity instanceof ElggGroup) {
		// assume the group is public
		$entity->leave($user);
		$options = array('relationship' => 'member',
				 'relationship_guid' => $entity->guid,
				 'inverse_relationship' => TRUE);
	}
	else {
		if (check_entity_relationship($user->guid ,'follow', $entity->guid)) {
			remove_entity_relationship($user->guid ,'follow', $entity->guid);
			$options = array('relationship' => 'follow',
					 'relationship_guid' => $entity->guid,
					 'inverse_relationship' => TRUE);

		}

	}
	if (!$is_local) {
		// check if this was the last follow and unsubscribe push if it is
		$remaining = elgg_get_entities_from_relationship($options);
		if (empty($remaining) && push_get_subscription($endpoint)) {
			push_unsubscribeto($endpoint);
		}
	}




