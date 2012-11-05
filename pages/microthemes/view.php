<?php
/**
 * Shows curent microtheme and lists possible new ones.
 *
 * @package ElggMicrothemes
 */

// access check for closed groups
group_gatekeeper();

$owner = elgg_get_page_owner_entity();
if (!$owner) {
	forward('profile/' . elgg_get_logged_in_user_entity()->name);
}

elgg_push_breadcrumb(elgg_echo('microthemes'));

$guid = get_input('guid');
$microtheme = get_entity($guid);

if ($microtheme) {
	// single entity view
	set_input('microtheme_guid', $microtheme->guid);
	$title = $microtheme->title;
	$content = elgg_view_entity($microtheme, true);
	elgg_push_breadcrumb($owner->name, 'microthemes/owner/' . $owner->guid);
	elgg_push_breadcrumb($microtheme->title);
}
else {
	/*if (is_plugin_enabled('file')) {
		file_register_toggle(); <-- gallery toggle
	}*/

	if ($owner->microtheme) {
		$owner_microtheme = get_entity($owner->microtheme);
		$selected = "selected: <a href='{$owner_microtheme->getURL()}'>".elgg_echo('microthemes')."</a>";
	}

	// list view
	elgg_register_title_button();

	elgg_push_breadcrumb($owner->name);

	if ($owner instanceof ElggUser) {
		$title = elgg_echo("microthemes:user", array($owner->name));
	} else {
		$title = elgg_echo("microthemes:group", array($owner->name));
	}

	// List files
	$content = elgg_list_entities(array(
		'types' => 'object',
		'subtypes' => 'microtheme',
		'full_view' => false,
		#'list_type' => 'gallery',
	));
	if (!$content) {
		$content = elgg_echo("microthemes:none");
	}
	$content = $selected . $content;
}


$params = array();

$sidebar = elgg_view('microthemes/sidebar');

$params['content'] = $content;
$params['title'] = $title;
$params['sidebar'] = $sidebar;
$params['filter'] = '';

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);
