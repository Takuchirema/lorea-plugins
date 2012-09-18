<?php

/*
 * push_notification
 *
 * a push notification has been received
 */
function push_notification($raw, $domain, $subscriber_id) {
        // Parse $raw and store the changed items for the subscription identified
        // by $domain and $subscriber_id
        $subscriber = ElggPuSHSubscription::load($domain, $subscriber_id);
        $xml = @ new SimpleXMLElement($raw);
        // try to update subscriber title
        $xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
        $xml->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');

        $subject_text = @current($xml->xpath("//activity:subject"));
        if ($subject_text) {
                $subscriber->entity->title = $subject_text;
                $subscriber->entity->save();
        }
        $salmon_link = @current($xml->xpath("//atom:link[attribute::rel='http://salmon-protocol.org/ns/salmon-replies']/@href"));
        if (!$salmon_link)
                $salmon_link = @current($xml->xpath("//atom:link[attribute::rel='salmon']/@href"));

        $entries = $xml->xpath("//atom:entry");
        $entries = array_reverse($entries);
        foreach($entries as $entry) {
                $entry->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
                $entry->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');
                $params = array('entry' => $entry,
				'subscriber' => $subscriber->entity,
				'salmon_link' => $salmon_link);
                trigger_plugin_hook('foreign_notification', 'foreign_notification', $params);
		error_log("PuSH notification arrived");
        }
}

/*
 * elgg page handler for this plugin
 */
function push_page_handler($page) {
	// Subscription page
	if ($page[0] == 'subscribe') {
		include(elgg_get_plugins_path() . "elgg-push/subscribe.php");
		return true;
	}

	// A notification has arrived for one of our subscribers
	//require_once $CONFIG->path . "engine/lib/api.php";
	//include_post_data();

	$subscriber_id = $page[0];
	$domain = 'elgg_subs';

	$sub = PuSHSubscriber::instance($domain, $subscriber_id, 'ElggPuSHSubscription', new ElggPuSHEnvironment());
	$sub->handleRequest('push_notification');
	return true;
}

function push_init() {
	elgg_extend_view('extensions/channel', 'push/channel');

	elgg_register_event_handler('created', 'river', array('PuSH', 'updateRiverEventHandler'));

	elgg_register_simplecache_view('push/channel');

	elgg_register_page_handler('push','push_page_handler');

	elgg_register_admin_menu_item('administer', 'push', 'administer_utilities');

	$action_path = elgg_get_plugins_path() . 'elgg-push/actions/push';
	elgg_register_action("push/subscribe", "$action_path/subscribe.php");
        elgg_register_action("push/delete_subscription", "$action_path/delete.php");

}

elgg_register_event_handler('init', 'system', 'push_init');
