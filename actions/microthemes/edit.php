<?php
/**
 * Elgg microtheme uploader/edit action
 *
 * @package ElggMicrothemes
 */

// Get variables
$title = get_input("title");
$access_id = (int) get_input("access_id");
$guid = (int) get_input('guid');
$tags = get_input("tags");

$assign_to = get_input("assign_to");

$topbar_color = get_input('topbar_color');
$background_color = get_input('background_color');

$repeat = get_input('repeat');
$alignment = get_input('alignment');

// get auto variables
$input = array();
$variables = elgg_get_config('microtheme');

foreach ($variables as $name => $field) {
	$input[$name] = get_input($name);
}


try {
	$input['height'] = (int)$input['height'];
}
catch (Except $e) {
	$input['height'] = 120;
}

try {
	$input['bg_y'] = (int)$input['bg_y'];
}
catch (Except $e) {
	$input['bg_y'] = 110;
}


try {
	$input['margin'] = (int)$input['margin'];
}
catch (Except $e) {
	$input['margin'] = 50;
}


elgg_make_sticky_form('microtheme');

// check if upload failed
if (!empty($_FILES['background_image']['name']) && $_FILES['background_image']['error'] != 0) {
	register_error(elgg_echo('microthemes:background:cannotload'));
	forward(REFERER);
}

// check whether this is a new file or an edit
$new = true;
if ($guid > 0) {
	$new = false;
}

if ($new) {
	$theme = new ElggObject();
	$theme->subtype = "microtheme";

	// if no title on new upload, grab filename
	if (empty($title)) {
		register_error('microthemes:notitle');
		forward(REFERER);
	}

} else {
	// load original microtheme object
	$theme = new ElggObject($guid);
	if (!$theme->guid) {
		register_error(elgg_echo('microthemes:cannotload'));
		forward(REFERER);
	}

	// user must be able to edit file
	if (!$theme->canEdit()) {
		register_error(elgg_echo('microthmees:noaccess'));
		forward(REFERER);
	}

	if (!$title) {
		// user blanked title, but we need one
		$title = $theme->title;
	}
}

$theme->title = $title;
$theme->access_id = ACCESS_PUBLIC;

if ($repeat && in_array('repeatx', $repeat))
	$theme->repeatx = 1;
else
	$theme->repeatx = 0;
if ($repeat && in_array('repeaty', $repeat))
	$theme->repeaty = 1;
else
	$theme->repeaty = 0;
$theme->bg_color = $background_color;
$theme->bg_alignment = $alignment;
$theme->height = $input['height'];
$theme->margin = $input['margin'];
$theme->bg_y = $input['bg_y'];
$theme->topbar_color = $topbar_color;
//$theme->hidesitename
//$theme->translucid_page


$tags = explode(",", $tags);
$theme->tags = $tags;
$theme->save();

// if we have a background upload, process it
if (isset($_FILES['background_image']['name']) && !empty($_FILES['background_image']['name'])) {

	$prefix = "microthemes/banner_{$theme->guid}";
	$file = new ElggFile();
	$file->owner_guid = $theme->owner_guid;
	$file->container_guid = $theme->owner_guid;
	$file->setFilename($prefix.'_master.jpg');
	$file->open("write");
	$file->write(get_uploaded_file('background_image'));
	$file->close();

	$guid = $theme->save();

	microthemes_create_thumbnails($theme, $file);
} else {
	// not saving a file but still need to save the entity to push attributes to database
	$theme->save();
}

// file saved so clear sticky form
elgg_clear_sticky_form('microtheme');


if ($guid) {
	system_message(elgg_echo("microthemes:saved"));
	if ($new) {
		add_to_river('river/object/microtheme/create', 'create', elgg_get_logged_in_user_guid(), $theme->guid);
	}
} else {
	register_error(elgg_echo("microthemes:nosave"));
}
forward('microthemes/owner/' . $assign_to);
