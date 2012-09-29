<?php

	$salmon_link = $vars['salmon'];
	$atom_endpoint = $vars['endpoint'];
	$icon = $vars['icon'];
	$hub = $vars['hub'];
	$author = $vars['author'];
	$title = $author['description'] . " " . $author['webpage'];
	$webid = $author['id'];

	$body = "<p><label>".elgg_echo("ostatus:subscribeto")."</label><br />";
	$body .= $author['name'].":"."<img width='96' height='96' src='".$icon."'/><br/><b>".$title."</b><br/>";
	//$body .= elgg_view('input/hidden', array('internalname' => 'atom_endpoint', 'value' => $atom_endpoint)); ???
	$body .= '<input type="hidden" name="atom_endpoint" value="'.htmlspecialchars($atom_endpoint).'" />';

	$body .= elgg_view('input/hidden', array('internalname' => 'salmon_endpoint', 'value' => $salmon_link));
	$body .= elgg_view('input/hidden', array('internalname' => 'hub', 'value' => $hub));
	$body .= elgg_view('input/hidden', array('internalname' => 'webid', 'value' => $webid));

        $body .= elgg_view("input/submit", array('value'=>'Confirm', 'internalname'=>'submit'))."</p>";

        echo $body;

