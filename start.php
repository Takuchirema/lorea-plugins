<?php

function ostatus_page_handler($page) {
	switch($page[0]) {
		case "subscribe":
			// subscribe, read arg uri
			$uri = get_input('uri');
			if ($uri) {
				$body = elgg_view("ostatus/confirm", array('uri'=>$uri));
			}
			else {
				$body = elgg_view("ostatus/subscribe");
			}
			echo elgg_view_page("", $body);
			break;
		default:
			break;
	}
	return true;
}

function ostatus_init() {

	// page handler
	elgg_register_page_handler('ostatus','ostatus_page_handler');

	elgg_extend_view("user/default", "ostatus/user");

	$action_path = elgg_get_plugins_path() . 'elgg-ostatus/actions/ostatus';
	elgg_register_action("ostatus/confirm", "$action_path/confirm.php");
	elgg_register_action("ostatus/unsubscribe", "$action_path/unsubscribe.php");
}

elgg_register_event_handler('init', 'system', 'ostatus_init');
