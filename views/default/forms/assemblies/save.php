<?php
/**
 * Edit assembly form
 *
 * @package Assemblies
 */

$assembly = get_entity($vars['guid']);
$vars['entity'] = $assembly;

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/assembly/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete elgg-state-disabled float-alt'
	));
}

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

$title_label = elgg_echo('title');
$title_input = elgg_view('input/text', array(
	'name' => 'title',
	'id' => 'assembly_title',
	'value' => $vars['title']
));

$excerpt_label = elgg_echo('assembly:excerpt');
$excerpt_input = elgg_view('input/text', array(
	'name' => 'excerpt',
	'id' => 'assembly_excerpt',
	'value' => html_entity_decode($vars['excerpt'], ENT_COMPAT, 'UTF-8')
));

$body_label = elgg_echo('assembly:body');
$body_input = elgg_view('input/longtext', array(
	'name' => 'description',
	'id' => 'assembly_description',
	'value' => $vars['description']
));

$save_status = elgg_echo('assembly:save_status');
if ($vars['guid']) {
	$entity = get_entity($vars['guid']);
	$saved = date('F j, Y @ H:i', $entity->time_created);
} else {
	$saved = elgg_echo('assembly:never');
}

$comments_label = elgg_echo('comments');
$comments_input = elgg_view('input/dropdown', array(
	'name' => 'comments_on',
	'id' => 'assembly_comments_on',
	'value' => $vars['comments_on'],
	'options_values' => array('On' => elgg_echo('on'), 'Off' => elgg_echo('off'))
));

$tags_label = elgg_echo('tags');
$tags_input = elgg_view('input/tags', array(
	'name' => 'tags',
	'id' => 'assembly_tags',
	'value' => $vars['tags']
));

$access_label = elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'name' => 'access_id',
	'id' => 'assembly_access_id',
	'value' => $vars['access_id']
));

$categories_input = elgg_view('input/categories', $vars);

// hidden inputs
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));


echo <<<___HTML

<div>
	<label for="assembly_title">$title_label</label>
	$title_input
</div>

<div>
	<label for="assembly_excerpt">$excerpt_label</label>
	$excerpt_input
</div>

<label for="assembly_description">$body_label</label>
$body_input
<br />

<div>
	<label for="assembly_tags">$tags_label</label>
	$tags_input
</div>

$categories_input

<div>
	<label for="assembly_comments_on">$comments_label</label>
	$comments_input
</div>

<div>
	<label for="assembly_access_id">$access_label</label>
	$access_input
</div>

<div class="elgg-foot">
	<div class="elgg-subtext mbm">
	$save_status <span class="assembly-save-status-time">$saved</span>
	</div>

	$guid_input

	$action_buttons
</div>

___HTML;
