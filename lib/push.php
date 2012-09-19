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
 * push_unsubscribeto
 *
 * unsubscribe to given url
 */
function push_unsubscribeto($url) {
        $subscription_id = sha1($url . get_site_secret());
        $sub = PuSHSubscriber::instance('elgg_subs', $subscription_id, 'ElggPuSHSubscription', new ElggPuSHEnvironment());
        if ($sub->unsubscribe($url,
                        $site_url . "push/". $subscription_id) != false) {
		push_delete_subscriptions($subscription_id);
		return true;
	}
	return false;
}

/*
 * Delete all subscriptions for given subscription id
 */
function push_delete_subscriptions($subscription_id) {
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
}

/*
 * push_create_subscription
 *
 * create the given subscription
 */
function push_create_subscription($subscription_id, $url) {
	$site_url = elgg_get_site_url();

	push_delete_subscriptions($subscription_id);

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
                return false;
        }
}

