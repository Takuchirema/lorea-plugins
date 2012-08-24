<?php

echo "<b>".elgg_echo("assemblies:agenda")."</b>";

$group = elgg_get_page_owner_entity();

// Get next assembly
$assembly = $vars['next_assembly'];
if (!empty($assembly)) {
	echo "<p>".date("m.d.y", $assembly->date)."</p>";
} else {
	echo elgg_echo("assemblies:none");
}
