<?php
/**
 * Edit assembly general properties form
 *
 * @package Assemblies
 */

$group = get_entity($vars['guid']);
$vars['entity'] = $group;

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

$vars['periodicity'] = $periodicity;
$vars['chat'] = $chat;
$vars['streaming_url'] = $streaming_url;
$vars['voip'] = $voip;

$action_buttons = '';

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button;

$periodicity_label = elgg_echo('assemblies:periodicity');
$periodicity_input = elgg_view('input/text', array(
	'name' => 'periodicity',
	'id' => 'assembly_periodicity',
	'value' => $vars['periodicity']
));
$chat_label = elgg_echo('assemblies:chat');
$chat_input = elgg_view('input/text', array(
	'name' => 'chat',
	'id' => 'assembly_chat',
	'value' => $vars['chat']
));
$streaming_url_label = elgg_echo('assemblies:streaming_url');
$streaming_url_input = elgg_view('input/text', array(
	'name' => 'streaming_url',
	'id' => 'assembly_streaming_url',
	'value' => $vars['streaming_url']
));
$voip_label = elgg_echo('assemblies:voip');
$voip_input = elgg_view('input/text', array(
	'name' => 'voip',
	'id' => 'assembly_voip',
	'value' => $vars['voip']
));

// hidden inputs
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));


echo <<<___HTML

$draft_warning

<div>
	<label for="assembly_periodicity">$periodicity_label</label>
	$periodicity_input
</div>

<div>
	<label for="assembly_chat">$chat_label</label>
	$chat_input
</div>

<div>
	<label for="assembly_streaming_url">$streaming_url_label</label>
	$streaming_url_input
</div>

<div>
	<label for="assembly_voip">$voip_label</label>
	$voip_input
</div>

<div class="elgg-foot">
	<div class="elgg-subtext mbm">
	$save_status <span class="assembly-save-status-time">$saved</span>
	</div>

	$guid_input

	$action_buttons
</div>

___HTML;
