<?php
/**
 * Assemblies module
 */

elgg_load_library('elgg:assemblies');

$group = elgg_get_page_owner_entity();

if ($group->assemblies_enable == "no") {
	return true;
}

$assembly = assemblies_get_next_assembly($group);

$all_link = elgg_view('output/url', array(
	'href' => "assembly/view/$assembly->guid",
	'text' => elgg_echo('assemblies:link:view'),
	'is_trusted' => true,
));

$all_link .= " ".elgg_view('output/url', array(
	'href' => "assembly/edit/$assembly->guid",
	'text' => elgg_echo('assemblies:link:edit'),
	'is_trusted' => true,
));


$info = elgg_view("assemblies/group_module_box", array_merge($vars, array(
	'next_assembly' => $assembly,
	'box' => 'info',
	'entity' => $group,
	'class' => 'elgg-col elgg-col-1of5',
)));

$agenda = elgg_view("assemblies/group_module_box", array_merge($vars, array(
	'next_assembly' => $assembly,
	'box' => 'agenda',
	'entity' => $group,
	'class' => 'elgg-col elgg-col-3of5',
)));

$minutes = elgg_view("assemblies/group_module_box", array_merge($vars, array(
	'box' => 'minutes',
	'entity' => $group,
	'class' => 'elgg-col elgg-col-1of5',
)));

$content = $info . $agenda . $minutes;

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo('assemblies:group'),
	'content' => $content,
	'all_link' => $all_link,
	#'add_link' => $new_link,
));
