<?php

	$salmon_link = $vars['salmon'];
	$atom_endpoint = $vars['endpoint'];
	$hub = $vars['hub'];
	$author = $vars['author'];
	$icon = $author['icon'];
	$webid = $author['id'];
	$tag = $vars['tag'];

	$body .= '<input type="hidden" name="atom_endpoint" value="'.htmlspecialchars($atom_endpoint).'" />';

	$body .= elgg_view('input/hidden', array('internalname' => 'salmon_endpoint', 'value' => $salmon_link));
	$body .= elgg_view('input/hidden', array('internalname' => 'hub', 'value' => $hub));
	$body .= elgg_view('input/hidden', array('internalname' => 'webid', 'value' => $webid));

        $body .= elgg_view("input/submit", array('value'=>'Unsubscribe', 'internalname'=>'submit'))."</p>";

        echo $body;

