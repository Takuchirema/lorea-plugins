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
	$image .= "<div class='elgg-avatar'><img width='96' height='96' src='".$icon."'/></div>";
	$body .= "<h3>".$author['name']."</h3> $title<br/><br/>";
	$body .= elgg_view("output/url", array('href' => $webid, 'text' => $webid));
	$body .= "</p>";

	$body = elgg_view('page/components/image_block', array('body'=>$body, 'image'=>$image));

	echo $body;
