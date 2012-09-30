<?php
	elgg_load_library('elgg:push');

	$user = get_loggedin_user();

	$uri = $vars['uri'];


	$feed = OstatusProtocol::getFeed($uri);
	$author = $feed->getAuthor();
	$endpoint = SalmonDiscovery::getYadisEndpoint($uri,
                        "//xrd:Link[attribute::type='application/atom+xml']/@href", 'application/atom+xml');
	$salmon_link = $feed->getSalmonEndpoint();
	$hub = $feed->getHub();
	$title = $feed->xpath(array('//atom:author/atom:title'));
	$feed->getIcons();

	$entity = FederatedObject::find($author['id']);
	$following = false;
	if ($entity) {
		if (check_entity_relationship($user->guid ,'follow', $entity->guid)) {
			$following = true;
		}
	}

	$args = array('author' => $author,
			'salmon' => $salmon_link,
			'endpoint' => $endpoint,
			'icon' => $feed->getIcon(),
			'title' => $title,
			'tag' => 'atom:author',
			'hub' => $hub);

	if ($following) {
		$msg = "ostatus:unsubscribeto";
	}
	else {
		$msg = "ostatus:subscribeto";
	}
	echo "<p><label>".elgg_echo("$msg:".$author['type'])."</label><br />";
	echo elgg_view('ostatus/profile', $args);

	if ($author['type'] == 'person') {
		if ($following)
			echo elgg_view_form('ostatus/unsubscribe', array(), $args);
		else
			echo elgg_view_form('ostatus/confirm', array(), $args);
	}
	else {
		echo elgg_echo("ostatus:cantsubscribe");
	}

