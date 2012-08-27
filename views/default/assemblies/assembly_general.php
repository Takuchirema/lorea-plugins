<?php

elgg_load_library('elgg:assemblies');

$entity_guid = $vars['entity'];
$group = get_entity($entity_guid);

// Grab variables
$periodicity = $group->assembly_periodicity;
$chat = $group->assembly_chat;
$streaming_url = $group->assembly_streaming;
$voip = $group->assembly_voip;
$assembly_location = $group->assembly_location;

// Grab next assembly and format title
$next_assembly = assemblies_get_next_assembly($group);
$title = date("d/m/y", $next_assembly->date);
$next_assembly_url = elgg_view('output/url', array(
                        'href' => "assembly/view/$next_assembly->guid",
                        'text' => $title,
                        ));

// Set defaults
if (empty($periodicity))
	$periodicity = 'undefined';
if (empty($chat))
	$chat = 'undefined';
if (empty($streaming_url))
	$streaming_url = 'undefined';
if (empty($voip))
	$voip = 'undefined';
if (empty($assembly_location))
	$assembly_location = 'undefined';

// Show general assembly settings
echo "<b>".elgg_echo('assemblies:assembly_location')."</b>";
echo ": " . $assembly_location;
echo "<br/>";
echo "<b>".elgg_echo('assemblies:periodicity')."</b>";
echo ": " . $periodicity;
echo "<br/>";
echo "<b>".elgg_echo('assemblies:chat')."</b>";
echo ": " . $chat;
echo "<br/>";
echo "<b>".elgg_echo('assemblies:streaming')."</b>";
echo ": " . $streaming_url;
echo "<br/>";
echo "<b>".elgg_echo('assemblies:voip')."</b>";
echo ": " . $voip;
echo "<br/>";
echo "<br/>";
echo "<b>".elgg_echo('assemblies:next')."</b>";
echo ": " . $next_assembly_url;
echo "<br/>";
