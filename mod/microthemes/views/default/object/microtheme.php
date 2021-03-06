<?php
/**
 * Microtheme renderer.
 *
 * @package ElggMicrothemes
 */

$full = elgg_extract('full_view', $vars, FALSE);
$microtheme = elgg_extract('entity', $vars, FALSE);

if (!$microtheme) {
	return TRUE;
}

$owner = $microtheme->getOwnerEntity();

$owner_link = elgg_view('output/url', array(
	'href' => "microthemes/owner/$owner->username",
	'text' => $owner->name,
	'is_trusted' => true,
));
$author_text = elgg_echo('byline', array($owner_link));

if ($microtheme->smallthumb) {
	$microtheme_icon = elgg_view_entity_icon($microtheme, 'medium');
}
else {
	$microtheme_icon = "<div style='background-color:black; width:150px; height:150px;'></div>";
}

$date = elgg_view_friendly_time($microtheme->time_created);

$metadata = elgg_view_menu('entity', array(
	'entity' => $vars['entity'],
	'handler' => 'microtheme',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

$subtitle = "$author_text $date $comments_link $categories";

// do not show the metadata and controls in widget view
if (elgg_in_context('widgets')) {
	$metadata = '';
}

$owner = elgg_get_page_owner_entity();

if ($full && !elgg_in_context('gallery')) {
	$params = array(
		'entity' => $microtheme,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
	);
	$params = $params + $vars;
	$summary = elgg_view('object/elements/summary', $params);

	foreach(array('height', 'margin', 'bg_color', 'topbar_color', 'repeatx', 'repeaty', 'bg_alignment') as $name) {
		$body .= "<label><b>".elgg_echo("microthemes:".$name).":</b></label> ";
		$body .= elgg_view('output/text', array('value' => $microtheme->$name));
		$body .= "<br />\n";
	}

	echo elgg_view('object/elements/full', array(
		'entity' => $microtheme,
		'title' => false,
		'icon' => $microtheme_icon,
		'summary' => $summary,
		'body' => $body,
	));

} elseif (elgg_in_context('gallery')) {
	echo '<div class="microtheme-gallery-item">';
	echo "<h3>" . $microtheme->title . "</h3>";
	echo $microtheme_icon;
	echo "<p class='subtitle'>$author_text $date</p>";
	echo '</div>';
} else {
	// brief view

	$params = array(
		'entity' => $microtheme,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'content' => $excerpt,
	);
	$params = $params + $vars;
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($microtheme_icon, $list_body);
}
