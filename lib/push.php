<?php

/*
 * push_subscribeto
 *
 * subscribe to given url
 */
function push_subscribeto($url) {
        $subscription_id = sha1($url . get_site_secret());
        return push_create_subscription($subscription_id, $url);
}

/*
 * push_create_subscription
 *
 * create the given subscription
 */
function push_create_subscription($subscription_id, $url) {
	$site_url = elgg_get_site_url();

        $metadata_pairs = array('subscriber_id' => $subscription_id);
        $options = array('metadata_name_value_pairs' => $metadata_pairs,
			 'types' => 'object',
			 'subtypes' => 'push_subscription');

        $entities = elgg_get_entities_from_metadata($options);

        if ($entities) {
        	foreach ($entities as $entity) {
                	$entity->delete();
	        }
        }

        $sub = PuSHSubscriber::instance('elgg_subs', $subscription_id, 'ElggPuSHSubscription', new ElggPuSHEnvironment());
        if ($xml = $sub->subscribe($url,
                        $site_url . "push/". $subscription_id)) {
                $subscription = ElggPuSHSubscription::load('elgg_subs', $subscribtion_id);
                if ($subscription) {
        	        $subscription->entity->title = @current($xml->xpath("//activity:subject"));
                	$subscription->entity->save();
                }
                return true;
        }
        else {
                error_log("created subscription failed");
                return false;
        }
}

/*
 * push_notification
 *
 * A push notification has been received
 * 
 * This function separates individual entries and triggers a hook
 * for each one.
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
                trigger_plugin_hook('push:notification', 'atom', $params);
        }
}

