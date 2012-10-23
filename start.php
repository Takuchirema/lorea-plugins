<?php

// General callbacks
function foreign_objects_notification($hook, $type, $return, $params) {
	// input parameters
	$entry = $params['entry'];
	$subscriber = $params['subscriber'];
	$salmon_link = $params['salmon_link'];

	$foreign = new ForeignObject();
	$foreign->load($entry);

	// parse verb
	$verb = $foreign->getVerb();

	// parse object type
	$object_type = $foreign->getObjectType();

	$target = $foreign->getObject();

	// output
	$params = array('notification' => $foreign,
			'subscriber' => $subscriber,
			'salmon_link' => $salmon_link,
			'entry' => $entry);
	trigger_plugin_hook('foreign_objects:'.$verb, $object_type, $params);
}

// Specific callbacks for river actions
function foreign_objects_action_post_article($hook, $type, $return, $params) {
	$foreign = $params['notification'];
	error_log("action: $hook $type");
}

function foreign_objects_init() {
	#elgg_register_library('elgg:push', elgg_get_plugins_path() . 'elgg-push/lib/push.php');

	elgg_register_plugin_hook_handler('push:notification', 'atom', 'foreign_objects_notification');

	elgg_register_plugin_hook_handler('foreign_objects:post', 'article', 'foreign_objects_action_post_article');
	elgg_register_plugin_hook_handler('foreign_objects:post', 'bookmark', 'foreign_objects_action_post_article');
	elgg_register_plugin_hook_handler('foreign_objects:post', 'note', 'foreign_objects_action_post_article');
}

elgg_register_event_handler('init', 'system', 'foreign_objects_init');
