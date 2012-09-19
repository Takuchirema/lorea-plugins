<?php

/*
 * elgg page handler for this plugin
 */
function push_page_handler($page) {
	// A notification has arrived for one of our subscribers
	elgg_load_library('elgg:push:download');

	$subscriber_id = $page[0];
	$domain = 'elgg_subs';

	$sub = PuSHSubscriber::instance($domain,
					$subscriber_id,
					'ElggPuSHSubscription',
					new ElggPuSHEnvironment());
	$sub->handleRequest('push_notification');
	return true;
}

function push_init() {
	elgg_register_library('elgg:push', elgg_get_plugins_path() . 'elgg-push/lib/push.php');
	elgg_register_library('elgg:push:download', elgg_get_plugins_path() . 'elgg-push/lib/download.php');

	elgg_extend_view('extensions/channel', 'push/channel');

	elgg_register_event_handler('created', 'river', array('PuSH', 'updateRiverEventHandler'));

	elgg_register_simplecache_view('push/channel');

	elgg_register_page_handler('push','push_page_handler');

	elgg_register_admin_menu_item('administer', 'push', 'administer_utilities');

	$action_path = elgg_get_plugins_path() . 'elgg-push/actions/push';
	elgg_register_action("push/subscribe", "$action_path/subscribe.php");
        elgg_register_action("push/delete", "$action_path/delete.php");
        elgg_register_action("push/download", "$action_path/download.php");

}

elgg_register_event_handler('init', 'system', 'push_init');
