<?php
	$body = "<p><label>".elgg_echo("push:input:atomurl")."</label><br />";
	$body .= elgg_view("input/text", array('value'=>'', 'internalname'=>'atom_url'));
	$body .= elgg_view("input/submit", array('value'=>'Submit', 'internalname'=>'submit'))."</p>";
	echo $body;
?>
