<?php
	elgg_load_library('elgg:push');

	$user = get_loggedin_user();

	$endpoint = get_input("atom_endpoint");
	$salmon_link = get_input("salmon_endpoint");
	$webid = get_input("webid");
	$hub = get_input("hub");

	$subscription = push_get_subscription($endpoint);
	if ($subscription)
		$has_subscription = "yes";
	else {
		push_subscribeto($endpoint);
		$has_subscription = "no";
	}

	$feed = OstatusProtocol::getFeed($webid);
	$author = $feed->getAuthor();

	$entity = FederatedObject::create($author, 'atom:author');
	$entity->salmon_link = $salmon_link;

	if (!check_entity_relationship($user->guid ,'follow', $entity->guid)) {
                add_entity_relationship($user->guid ,'follow', $entity->guid);
		error_log("FOLLOW! $endpoint $salmon_link $webid $hub $has_subscription");
	}

	error_log("CONFIRM! $endpoint $salmon_link $webid $hub $has_subscription");
