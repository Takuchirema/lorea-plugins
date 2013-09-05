<?php
/**
 * Push import/download function library
 */

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
	push_import_atom_activitystreams($raw, $subscriber->entity);
}

/*
 * Trigger plugin hooks from an incoming activitystreams feed.
 */
function push_import_atom_activitystreams($data, $obj=null) {
	$xml = @ new SimpleXMLElement($data);
	if (empty($xml)) {
		error_log("CANT PARSE XML");
		return array();
	}
	$xml->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
	$xml->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');
	$subject_text = @current($xml->xpath("//activity:subject/atom:summary"));
	if ($obj && $subject_text) {
		$obj->title = $subject_text;
		$obj->save();
	}
	$salmon_link = @current($xml->xpath("//atom:link[attribute::rel='http://salmon-protocol.org/ns/salmon-replies']/@href"));
	if (!$salmon_link)
		$salmon_link = @current($xml->xpath("//atom:link[attribute::rel='salmon']/@href"));
	$entries = $xml->xpath("//atom:entry");
	$entries = array_reverse($entries);
	if (empty($entries)) {
		error_log("NO ENTRIES");
		return array();
	}
        foreach($entries as $entry) {
                $entry->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
                $entry->registerXPathNamespace('activity', 'http://activitystrea.ms/spec/1.0/');
		$params = array('entry'=>$entry, 'salmon_link'=>$salmon_link);
                elgg_trigger_plugin_hook('push:notification', 'atom', $params);
        }
	return $entries;
}

/*
 * Import activity streams data and show elgg feedback.
 */
function elgg_import_atom_activitystreams($data, $obj=null) {
	$entries = push_import_atom_activitystreams($data, $obj);
	system_message(elgg_echo("push:imported", array(count($entries))));
 	forward("admin/administer_utilities/push");
}

/*
 * Download an activity streams feed from the given url
 */
function push_download_atom_activitystreams($url, $obj) {
	$request = curl_init($url);
	curl_setopt($request, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, TRUE);

    	$data = curl_exec($request);
    	$ret_code = curl_getinfo($request, CURLINFO_HTTP_CODE);
	if ($ret_code == 200 || $ret_code == 302) {
		return push_import_atom_activitystreams($data, $obj);
	}
	else
		return false;
}

/*
 * Download an activity streams feed from the given url
 */
function elgg_download_atom_activitystreams($url, $obj) {
	$entries = push_download_atom_activitystreams($url, $obj);
	if ($entries) {
		system_message(elgg_echo("push:imported", array(count($entries))));
	}
	else {
		register_error(elgg_echo('push:couldntdownload'));
	}

}


