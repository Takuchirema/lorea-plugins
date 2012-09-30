<?php
	$salmon_link = $vars['salmon'];
	$atom_endpoint = $vars['endpoint'];
	$hub = $vars['hub'];
	$author = $vars['author'];
	$icon = $author['icon'];
	$tag = $vars['tag'];

	$author = FederatedPerson::getPoco($author['notification'], 'atom:author', $author);
	$title = $author['description']; # . " " . $author['webpage'];
	$webid = $author['id'];

	$body = "<p>";
	$body .= $author['name'].":"."<img width='96' height='96' src='".$icon."'/><br/><b>".$title."</b><br/>";
	$body .= elgg_view("output/url", array('href'=>$author['id'], 'text'=>$author['id']));
	$body .= "</p>";

	echo $body;
