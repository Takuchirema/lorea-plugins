<?php
/**
 * Elgg Microthemes
 *
 * @package ElggMicrothemes
 */
 
elgg_register_event_handler('init', 'system', 'microthemes_init');

/**
 * Initialize the microthemes plugin.
 *
 */
function microthemes_init(){
	
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('microthemes', 'microthemes_page_handler');
	
	// Register some actions
	$action_base = elgg_get_plugins_path() . 'microthemes/actions/microthemes';
	elgg_register_action("microthemes/edit", "$action_base/edit.php");
	elgg_register_action("microthemes/delete", "$action_base/delete.php");
	elgg_register_action("microthemes/choose", "$action_base/choose.php");
	elgg_register_action("microthemes/clear", "$action_base/clear.php");
	
	// Register URL handlers for files
	elgg_register_entity_url_handler('object', 'microtheme', 'microthemes_url_override');
	elgg_register_plugin_hook_handler('entity:icon:url', 'object', 'microthemes_icon_url_override');
	
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'microthemes_user_hover_menu');
	
	elgg_register_event_handler('pagesetup', 'system', 'microthemes_pagesetup');
	
	// register the color picker's JavaScript
	$colorpicker_js = elgg_get_simplecache_url('js', 'input/color_picker');
	elgg_register_simplecache_view('js/input/color_picker');
	elgg_register_js('elgg.input.colorpicker', $colorpicker_js);
	
	// register the color picker's CSS
	$colorpicker_css = elgg_get_simplecache_url('css', 'input/color_picker');
	elgg_register_simplecache_view('css/input/color_picker');
	elgg_register_css('elgg.input.colorpicker', $colorpicker_css);
	
	elgg_register_menu_item('page', array(
		'name' => 'choose_profile_microtheme',
		'href' => "microthemes/owner/" . elgg_get_page_owner_entity()->guid,
		'text' => elgg_echo('microthemes:profile:edit'),
		'contexts' => array('profile_edit'),
	));
	
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'microthemes_entity_menu_setup', 1000);

	elgg_extend_view("page/elements/head", "microthemes/metatags");

	$variables = array(
		'height' => 'text',
		'margin' => 'text',
		'bg_y' => 'text',
	);
	elgg_set_config('microtheme', $variables);

	elgg_register_event_handler('upgrade', 'system', 'microthemes_run_upgrades');

}

/*
 $microtheme: microtheme entity
 $file: $file with master file
*/
function microthemes_create_thumbnails($microtheme, $file) {
	$microtheme->icontime = time();
	$prefix = "microthemes/banner_".$microtheme->guid;
		
	$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
	if ($thumbnail) {
		$thumb = new ElggFile();
		$thumb->setMimeType($_FILES['upload']['type']);

		$thumb->setFilename($prefix."medium");
		$thumb->open("write");
		$thumb->write($thumbnail);
		$thumb->close();

		$microtheme->thumbnail = $prefix."medium";
		unset($thumbnail);
	}

	$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 150, 150, true);
	if ($thumbsmall) {
		$thumb->setFilename($prefix."small");
		$thumb->open("write");
		$thumb->write($thumbsmall);
		$thumb->close();
		$microtheme->smallthumb = $prefix."small";
		unset($thumbsmall);
	}

	$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
	if ($thumblarge) {
		$thumb->setFilename($prefix."large");
		$thumb->open("write");
		$thumb->write($thumblarge);
		$thumb->close();
		$microtheme->largethumb = $prefix."large";
		unset($thumblarge);
	}
}

function microthemes_run_upgrades() {
	if (include_once(elgg_get_plugins_path() . 'upgrade-tools/lib/upgrade_tools.php')) {
		upgrade_module_run('microthemes');
	}

}


function microthemes_url_override($entity) {
	$owner = elgg_get_page_owner_entity();

	$site_url = elgg_get_site_url();
	return $site_url . "microthemes/view/" . "$owner->guid/$entity->guid";
	
}

function microthemes_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
                return $return;
        }

        $entity = $params['entity'];
	if ($entity->getSubtype() != 'microtheme') {
		return $return;
	}
	$owner = elgg_get_page_owner_entity();

	if ($owner->microtheme == $entity->guid) {
		$options = array(
				'name' => 'clear',
				'text' => elgg_echo('microthemes:clear'),
				'title' => elgg_echo('microthemes:clear:tooltip'),
				'href' => "action/microthemes/clear?guid=$entity->guid&assign_to=$owner->guid",
				'is_action' => true
			);
	}
	else {
		$options = array(
				'name' => 'choose',
				'text' => elgg_echo('microthemes:choose'),
				'title' => elgg_echo('microthemes:choose:tooltip'),
				'href' => "action/microthemes/choose?guid=$entity->guid&assign_to=$owner->guid",
				'is_action' => true
			);

	}

        $return[] = ElggMenuItem::factory($options);

        // likes count
        if ($entity->canEdit()) {
                $options = array(
                        'name' => 'edit',
                        'text' => elgg_echo('microthemes:edit'),
                        'title' => elgg_echo('microthemes:edit:tooltip'),
                        'href' => "microthemes/edit/$entity->guid?assign_to=$owner->guid",
                        'priority' =>50,
                );
                $return[] = ElggMenuItem::factory($options);
        }

        return $return;
}

/**
 * Dispatcher for microthemes.
 * URLs take the form of
 *  @todo All microthemes:        microthemes/all
 *  @todo User's microtheme:      microthemes/owner/<username>
 *  @todo Friends' microthemes:   microthemes/friends/<username>
 *  View CSS:               microthemes/css/<guid>/
 *  New microtheme:         microthemes/add/<guid> (container: user, group)
 *  Edit microtheme:        microthemes/edit/<guid>
 *  Group microthemes:      microthemes/group/<guid>/all
 *
 * @param array $page
 * @return bool
 */
function microthemes_page_handler($page) {
	elgg_push_context('microthemes');
	$page_base = elgg_get_plugins_path() . "microthemes/pages/microthemes";
	switch ($page[0]) {
		case 'css':
			elgg_set_page_owner_guid($page[1]);
			include("$page_base/css.php");
			break;
		case 'add':
			elgg_set_page_owner_guid($page[1]);
			set_input('assign_to', $page[1]);
			include("$page_base/new.php");
			break;
		case 'edit':
			set_input('guid', $page[1]);
			$entity = get_entity($page[1]);
			elgg_set_page_owner_guid($entity->container_guid);
			include("$page_base/edit.php");
			break;
		case 'group':
		case 'owner':
			elgg_set_page_owner_guid($page[1]);
			set_input('assign_to', $page[1]);
			include("$page_base/view.php");
			break;
		default:
			if (!get_input('assign_to')) {
				set_input('assign_to', elgg_get_logged_in_user_guid());
			}
			set_input('guid', $page[2]);
			elgg_set_page_owner_guid($page[1]);
			include("$page_base/view.php");
			break;
	}
	return true;
}


function microthemes_icon_url_override($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	if ($entity->getSubtype() == 'microtheme') {
		return elgg_get_site_url() . 'mod/microthemes/thumbnail.php?guid=' . $entity->guid . '&last_updated=' . $entity->time_updated;
	}
	return $returnvalue;
}

function microthemes_pagesetup() {
	$owner = elgg_get_page_owner_entity();
	if ($owner && $owner->canEdit() && $owner instanceof ElggGroup) {
		elgg_register_menu_item('page', array(
			'name' => 'choose_profile_microtheme',
			'href' => "microthemes/owner/" . $owner->guid,
			'text' => elgg_echo('microthemes:profile:edit'),
			'contexts' => array('profile_edit', 'settings'),
		));
		// need to check for groups context like this otherwise the item appears on the main page for the group.
		if (elgg_get_context() == 'groups') {
			elgg_register_menu_item('page', array(
				'name' => 'microthemes',
				'text' => elgg_echo('microthemes:group:edit'),
				'href' => "microthemes/group/" . $owner->guid,
			));
		}
	}
}

function microthemes_user_hover_menu($hook, $type, $return, $params) {
	$user = $params['entity'];
	if ($user->canEdit()) {
		$url = "microthemes/owner/$user->guid";
		$item = new ElggMenuItem('microthemes:profile:edit', elgg_echo('microthemes:profile:edit'), $url);
		$item->setSection('action');
		$return[] = $item;
	}
	return $return;
}
