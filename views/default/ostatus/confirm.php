<?php
	elgg_load_library('elgg:push');

	$user = elgg_get_logged_in_user_entity();

	$uri = $vars['uri'];

	if (FederatedObject::isLocalID($uri)) {
		$id = $uri;
		$entity = FederatedObject::find($id);
		$icon = $entity->getIcon();

		$is_local = true;
		$name = $entity->name;
		$type = $entity->getType();
		if ($entity instanceof ElggUser) {
			$type = 'person';
		}
	} else {
		$feed = OstatusProtocol::getFeed($uri);
		$author = $feed->getAuthor();
		$endpoint = SalmonDiscovery::getYadisEndpoint($uri,
				"//xrd:Link[attribute::type='application/atom+xml']/@href", 'application/atom+xml');
		$salmon_link = $feed->getSalmonEndpoint();
		$hub = $feed->getHub();
		$title = $feed->xpath(array('//atom:author/atom:title'));
		$feed->getIcons();
		$icon = $feed->getIcon();

		$id = $author['id'];

		$is_local = false;
		$entity = FederatedObject::find($id);
		$name = $author['name'];
		$type = $author['type'];
	}


	$following = false;
	if ($entity instanceof ElggGroup) {
		if ($entity->isMember($user))
			$following = true;
	}
	else {
		if (check_entity_relationship($user->guid ,'follow', $entity->guid)) {
			$following = true;
		}
	}

	$args = array('webid' => $id,
			'salmon' => $salmon_link,
			'endpoint' => $endpoint,
			'icon' => $icon,
			'title' => $title,
			'tag' => 'atom:author',
			'hub' => $hub);

	if ($following) {
		$msg = "ostatus:unsubscribeto";
	}
	else {
		$msg = "ostatus:subscribeto";
	}
	echo "<p><label>".elgg_echo("$msg:".$type)."</label><br />";
	if ($is_local) {
		$args['entity'] = $entity;
		echo elgg_view('ostatus/profile_local', $args);
	}
	else {
		$args['author'] = $author;
		echo elgg_view('ostatus/profile', $args);
	}

	if ($type == 'person' || $type == 'group') {
		if ($following)
			echo elgg_view_form('ostatus/unsubscribe', array(), $args);
		else
			echo elgg_view_form('ostatus/confirm', array(), $args);
	}
	else {
		echo elgg_echo("ostatus:cantsubscribe");
	}

