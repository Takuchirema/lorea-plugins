<?php

// General callbacks
function federated_objects_notification($hook, $type, $return, $params) {
	// input parameters
	$entry = $params['entry'];
	$subscriber = $params['subscriber'];
	$salmon_link = $params['salmon_link'];

	$federated = new FederatedObject();
	$federated->load($entry);

	// parse verb
	$verb = $federated->getVerb();

	// parse object type
	$object_type = $federated->getObjectType();

	$target = $federated->getObject();

	// output
	$params = array('notification' => $federated,
			'subscriber' => $subscriber,
			'salmon_link' => $salmon_link,
			'entry' => $entry);
	trigger_plugin_hook('federated_objects:'.$verb, $object_type, $params);
}

// Specific callbacks for river actions
function federated_objects_action_post_article($hook, $type, $return, $params) {
	$federated = $params['notification'];
	error_log("action: $hook $type");
}

function federated_objects_init() {
	#elgg_register_library('elgg:push', elgg_get_plugins_path() . 'elgg-push/lib/push.php');

	elgg_register_plugin_hook_handler('push:notification', 'atom', 'federated_objects_notification');

	// callbacks for specific actions
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', 'federated_objects_action_post_article');
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', 'federated_objects_action_post_article');
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', 'federated_objects_action_post_article');
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', 'federated_objects_action_post_article');
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
