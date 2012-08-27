<?php

$entity_guid = $vars['entity'];
$group = get_entity($entity_guid);

// Grab variables
$periodicity = $group->assembly_periodicity;
$chat = $group->assembly_chat;
$streaming_url = $group->assembly_streaming;
$voip = $group->assembly_voip;

// Set defaults
if (empty($periodicity))
	$periodicity = 'undefined';
if (empty($chat))
	$chat = 'undefined';
if (empty($streaming_url))
	$streaming_url = 'undefined';
if (empty($voip))
	$voip = 'undefined';

// Show general assembly settings
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
