<?php

echo "<b>".elgg_echo("assemblies:agenda")."</b>";

$group = elgg_get_page_owner_entity();

// Get next assembly
$assembly = $vars['next_assembly'];
if (!empty($assembly)) {
	$children = $assembly->getChildren();
	echo "<ul>";
	foreach ($children as $child) {
		echo "<li>".$child->getTitleLink()."</li>";
	}
	echo "</ul>";

	echo "<hr /><p>".elgg_view('output/url', array(
		'href' => "decision/add/$assembly->guid",
		'text' => ucfirst(elgg_echo('decision:add'))."</p>",
	));


} else {
	echo "<p>".elgg_echo("assemblies:none")."</p>";
}

