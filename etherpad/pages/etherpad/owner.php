<?php
/**
 * Elgg etherpad plugin everyone page
 *
 * @package etherpad
 */

if(!elgg_get_page_owner_guid()){
	elgg_set_page_owner_guid(elgg_get_logged_in_user_guid());
}

$page_owner = elgg_get_page_owner_entity();

elgg_push_breadcrumb($page_owner->name);

elgg_register_title_button();

$offset = (int)get_input('offset', 0);
$content .= elgg_list_entities(array(
	'type' => 'object',
	'subtype' => 'etherpad',
	'container_guid' => $page_owner->guid,
	'limit' => 10,
	'offset' => $offset,
	'full_view' => false,
	'view_toggle_type' => false
));

if (!$content) {
	$content = elgg_echo('etherpad:none');
}

$title = elgg_echo('etherpad:owner', array($page_owner->name));

$filter_context = '';
if ($page_owner->getGUID() == elgg_get_logged_in_user_guid()) {
	$filter_context = 'mine';
}

$vars = array(
	'filter_context' => $filter_context,
	'content' => $content,
	'title' => $title,
	'sidebar' => elgg_view('etherpad/sidebar'),
);

// don't show filter if out of filter context
if ($page_owner instanceof ElggGroup) {
	$vars['filter'] = false;
}

$body = elgg_view_layout('content', $vars);

echo elgg_view_page($title, $body);
?>
