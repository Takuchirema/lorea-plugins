<?php

	$salmon_link = $vars['salmon'];
	$atom_endpoint = $vars['endpoint'];
	$hub = $vars['hub'];
	$author = $vars['author'];
	$webid = $vars['webid'];
	$tag = $vars['tag'];

	$body .= '<input type="hidden" name="atom_endpoint" value="'.htmlspecialchars($atom_endpoint).'" />';

	$body .= elgg_view('input/hidden', array('internalname' => 'salmon_endpoint', 'value' => $salmon_link));
	$body .= elgg_view('input/hidden', array('internalname' => 'hub', 'value' => $hub));
	$body .= elgg_view('input/hidden', array('internalname' => 'webid', 'value' => $webid));

        $body .= elgg_view("input/submit", array('value'=>'Confirm', 'internalname'=>'submit'))."</p>";

        echo $body;

