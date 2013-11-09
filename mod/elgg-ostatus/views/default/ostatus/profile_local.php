<?php
	$hub = $vars['hub'];
	$icon = $vars['icon'];
	$tag = $vars['tag'];
	$entity = $vars['entity'];
	$webid = $vars['id'];

	$title = $entity->description;

	$body = "<p>";
	$image .= "<div class='elgg-avatar'><img width='96' height='96' src='".$icon."'/></div>";
	$body .= "<h3>".$entity->name."</h3> $title<br/><br/>";
	$body .= elgg_view("output/url", array('href' => $webid, 'text' => $webid));
	$body .= "</p>";

	$body = elgg_view('page/components/image_block', array('body'=>$body, 'image'=>$image));

	echo $body;
