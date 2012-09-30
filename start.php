<?php

function ostatus_page_handler($page) {
	switch($page[0]) {
		case "subscribe":
			set_context("ostatus");
			$title = elgg_echo('ostatus:subscribe');
			// subscribe, read arg uri
			$uri = get_input('uri');
			if ($uri) {
				$body = elgg_view("ostatus/confirm", array('uri'=>$uri));
			}
			else {
				$body = elgg_view("ostatus/subscribe");
			}
			$body = elgg_view_layout('content', array(
				'filter' => '',
				'content' => $body,
				'title' => $title,
				//'sidebar' => elgg_view('pages/sidebar/navigation'),
			));
			echo elgg_view_page($title, $body);
			break;
		case "activity":
			// site activity for following
			set_context("activity");
			$title = elgg_echo('ostatus:activity');
			$body = elgg_view('ostatus/activity');
			echo elgg_view_page($title, $body);
			break;
		default:
			break;
	}
	return true;
}

function ostatus_prepare_menu($hook, $type, $return, $params) {
	if (!in_array(get_context(), array('activity'))) {
		return $return;
	}
	$page = current_page_url();
	$site_url = elgg_get_site_url();
	$selected = false;
	// check to see if we're on the ostatus/activity page
	if (strpos($page, 'ostatus/activity') !== false) {
		$selected = true;
	}
	$tab = array(
		'name' => 'following',
		'text' => elgg_echo('ostatus:following'),
		'href' => "ostatus/activity",
		'selected' => $selected,
		'priority' => 500,
	);
	elgg_register_menu_item('filter', $tab);
	return $return;
}

/**
 * Ostatus user hover menu
 */
function ostatus_user_hover_menu($hook, $type, $return, $params) {
        $user = $params['entity'];
	$logged = get_loggedin_user();

        if (elgg_is_logged_in() && elgg_get_logged_in_user_guid() != $user->guid) {
		if ($user->foreign && $user->atom_id) {
			$url = "ostatus/subscribe?uri={$user->atom_id}";
			if (check_entity_relationship($logged->guid, 'follow', $user->guid)) {
				$text = elgg_echo('ostatus:follow:remove');
				$name = 'remove_follow';
			}
			else {
				$text = elgg_echo('ostatus:follow:add');
				$name = 'add_follow';
			}

			//$url = elgg_add_action_tokens_to_url($url);
			$item = new ElggMenuItem($name, $text, $url);
			$item->setSection('action');
			$return[] = $item;
		}
        }

        return $return;
}


function ostatus_init() {

	// page handler
	elgg_register_page_handler('ostatus','ostatus_page_handler');

	// extend views
	elgg_extend_view("user/default", "ostatus/user");
	elgg_extend_view("page/elements/sidebar", "ostatus/sidebar");
	elgg_extend_view('css/elgg', 'ostatus/css');

	// actions
	$action_path = elgg_get_plugins_path() . 'elgg-ostatus/actions/ostatus';
	elgg_register_action("ostatus/confirm", "$action_path/confirm.php");
	elgg_register_action("ostatus/unsubscribe", "$action_path/unsubscribe.php");

	// create menu items before layout
	elgg_register_plugin_hook_handler('output:before', 'layout', 'ostatus_prepare_menu');

	// Extend avatar hover menu
	elgg_register_plugin_hook_handler('register', 'menu:user_hover', 'ostatus_user_hover_menu');

}

elgg_register_event_handler('init', 'system', 'ostatus_init');
