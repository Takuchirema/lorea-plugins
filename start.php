<?php
/**
         * Elgg powered plugin
         * 
         * @package
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author lorea
         * @copyright lorea
         * @link http://lorea.cc
         */

	function microthemes_tasksicon_hook($hook, $entity_type, $returnvalue, $params) {
		if ($hook == 'entity:icon:url') {
			$entity = $params['entity'];
			if ($entity->getSubtype() == 'microthemes') {
				return $entity->bgurl;
			}
		}
		return $returnvalue;
	}

	function microthemes_pagesetup() {
		global $CONFIG;
		$page_owner = page_owner_entity();
		if ($page_owner instanceof ElggGroup && get_context() == "groups" && $page_owner->canEdit()) {
			add_submenu_item(elgg_echo("microthemes:groupthemes"), 
					$CONFIG->wwwroot . "pg/microthemes/groupview/".$page_owner->getGUID());
		}
	}

	function microthemes_page_handler($page) {
		global $CONFIG;
		set_context('microthemes');
		switch ($page[0]) {
			case 'css':
				include($CONFIG->pluginspath.'microthemes/css.php');
				break;
			case 'edit':
				if ($page[1]) {
					set_input('object_guid', $page[1]);
				}
				include($CONFIG->pluginspath.'microthemes/edit.php');
				break;
			case 'groupview':
				set_input('assign_to', $page[1]);
				include($CONFIG->pluginspath.'microthemes/view.php');
				break;
			default:
				if (!get_input('assign_to'))
					set_input('assign_to', get_loggedin_userid());
				include($CONFIG->pluginspath.'microthemes/view.php');
				break;
		}
	}

 	function microthemes_init(){
			global $CONFIG;
			register_action("microthemes/delete",false, $CONFIG->pluginspath . "microthemes/actions/microthemes/delete.php");
			register_action("microthemes/clear",false, $CONFIG->pluginspath . "microthemes/actions/microthemes/clear.php");
			register_action("microthemes/edit",false, $CONFIG->pluginspath . "microthemes/actions/microthemes/edit.php");
			register_action("microthemes/choose",false, $CONFIG->pluginspath . "microthemes/actions/microthemes/choose.php");
			register_plugin_hook('entity:icon:url', 'object', 'microthemes_tasksicon_hook');
			register_page_handler('microthemes','microthemes_page_handler');
			register_elgg_event_handler('pagesetup','system','microthemes_pagesetup');

                        elgg_extend_view("metatags", "microthemes/metatags");
			elgg_extend_view('profile/menu/linksownpage','microthemes/profilemenu');
	}

register_elgg_event_handler('init','system','microthemes_init');

?>
