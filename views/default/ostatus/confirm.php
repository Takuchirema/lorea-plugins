<?php
	elgg_load_library('elgg:push');

	$user = get_loggedin_user();

	$uri = $vars['uri'];

	$endpoint = SalmonDiscovery::getYadisEndpoint($uri,
                        "//xrd:Link[attribute::type='application/atom+xml']/@href");
	if (push_get_subscription($endpoint))
		echo "HAS SUBSCRIPTION";
	$feed = OstatusProtocol::getFeed($uri);
	$author = $feed->getAuthor();
	$salmon_link = $feed->getSalmonEndpoint();
	$hub = $feed->getHub();
	$title = $feed->xpath(array('//atom:author/atom:title'));

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
			'hub' => $hub);
	if ($following)
		echo elgg_view_form('ostatus/unsubscribe', array(), $args);
	else
		echo elgg_view_form('ostatus/confirm', array(), $args);
	//echo "<p><b>".$author['id']."::$salmon_link::$hub</b></p>";

	//echo $xrds."XX".$bla."::".$salmon."::".$endpoint;

