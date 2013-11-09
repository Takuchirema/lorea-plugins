<?php
/**
 * Edit a microtheme
 *
 * @package ElggMicrothemes
 */

gatekeeper();

$guid = (int) get_input('guid');
$microtheme = get_entity($guid);
if (!$microtheme) {
	forward();
}
if (!$microtheme->canEdit()) {
	forward();
}
set_input('microtheme_guid', $guid);
$assign_to = (int) get_input('assign_to');

$title = elgg_echo('microthemes:edit');
$owner = get_entity($assign_to);

elgg_push_breadcrumb(elgg_echo('microthemes'));
elgg_push_breadcrumb($owner->name, 'microthemes/owner/' . $owner->guid);
elgg_push_breadcrumb($microtheme->title, $microtheme->getURL());
elgg_push_breadcrumb($title);

elgg_set_page_owner_guid($microtheme->getContainerGUID());

$form_vars = array('enctype' => 'multipart/form-data');
$body_vars = array('assign_to' => $assign_to, 'guid' => $guid);

$content = elgg_view_form('microthemes/edit', $form_vars, $body_vars);

$body = elgg_view_layout('content', array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
));

echo elgg_view_page($title, $body);
