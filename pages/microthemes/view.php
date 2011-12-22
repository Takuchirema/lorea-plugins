<?php
/**
 * Shows curent microtheme and lists possible new ones.
 *
 * @package ElggMicrothemes
 */

$owner = elgg_get_page_owner_entity();

if(!$owner) {
	forward(REFERER);
}

// access check for closed groups


$title = elgg_echo('microthemes:owner', array($owner->name));

elgg_push_breadcrumb(elgg_echo('microthemes'));
elgg_push_breadcrumb($owner->name);

elgg_register_title_button();

$content = elgg_list_entities(array(
	'types' => 'object',
	'subtypes' => 'microtheme',
	'full_view' => false,
	'list_type' => 'gallery',
));
if (elgg_get_entities(array('type' => 'object', 'subtype' => 'microtheme', 'count' => true)) == 0) {
	$content = '<p>' . elgg_echo('microthemes:none') . '</p>';
}

$params = array(
	'content' => $content,
	'title' => $title,
	'filter' => '',
);

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
