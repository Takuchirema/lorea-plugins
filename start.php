<?php
/**
* Elgg microthemes plugin
* This plugin lets users send messages to each other.
*
* @package ElggMicrothemes
*/


elgg_register_event_handler('init', 'system', 'microthemes_init');

function microthemes_init() {

	// register a library of helper functions
	elgg_register_library('elgg:microthemes', elgg_get_plugins_path() . 'microthemes/lib/microthemes.php');

	// Extend system CSS with our own styles, which are defined in the microthemes/css view
	elgg_extend_view('css/elgg', 'microthemes/css');
	elgg_extend_view('js/elgg', 'microthemes/js');
	
	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('microthemes', 'microthemes_page_handler');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'microthemes/actions/microthemes';
	elgg_register_action("microthemes/edit", "$action_path/edit.php");
	elgg_register_action("microthemes/delete", "$action_path/delete.php");
	elgg_register_action("microthemes/choose", "$action_path/choose.php");
	elgg_register_action("microthemes/clear", "$action_path/clear.php");
}

/**
 * Messages page handler
 *
 * @param array $page Array of URL components for routing
 * @return bool
 */
function microthemes_page_handler($page) {

	elgg_set_context('microthemes');
	
	$base_dir = elgg_get_plugins_path() . 'microthemes/pages/microthemes';
	
	switch ($page[0]) {
		case 'css':
			include("$base_dir/css.php");
			break;
		case 'edit':
			set_input('guid', $page[1]);
			include("$base_dir/edit.php");
			break;
		case 'view':
			set_input('assign_to', $page[1]);
			include("$base_dir/view.php");
			break;
		default:
			return false;
	}
	return true;
}

