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

function federated_objects_action_post_note($hook, $type, $return, $params) {
	$notification = $params['notification'];
	$subscriber = $params['subscriber'];
	$entry = $params['entry'];

	$author = $notification->getAuthor();
	$object = $notification->getObject();

	$object['author_entity'] = $author;

	$author = FederatedObject::create($author);
	$object['owner_entity'] = $object;
	$note = FederatedObject::create($object);
	error_log("note: $hook $type");
}

function fo_randomString($length)
{
    // Generate random 32 charecter string
    $string = md5(time());

    // Position Limiting
    $highest_startpoint = 32-$length;

    // Take a random starting point in the randomly
    // Generated String, not going any higher then $highest_startpoint
    $randomString = substr($string,rand(0,$highest_startpoint),$length);

    return $randomString;

}

function federated_objects_create_person($params, $entity) {
	if ($entity) {
		error_log("federated_objects_create_person:exists!");
	}
	else {
		error_log("federated_objects_create_person:doesnt exists!". $params['id']);
		$access = elgg_set_ignore_access(true);
		$entity = new ElggUser();
		$entity->owner_guid = 0;
		$entity->container_guid = 0;
		$entity->subtype = 'ostatus';
		$entity->username = fo_randomString(8);
		$entity->save();
		$entity->username = 'ostatus_'.$entity->getGUID();
		$entity->name = $params['name'];
                $entity->access_id = ACCESS_PUBLIC;
		$entity->atom_id = $params['id'];
		$entity->foreign = true;
		$entity->save();
		elgg_set_ignore_access($access);
	}
	return $entity;
}

function federated_objects_create_note($params, $entity) {
	error_log("create note!!");
	$owner = $params['owner_entity'];
	$entry = $params['entry'];
	$access_id = ACCESS_PUBLIC;
	$method = 'ostatus';

	$body = @current($entry->xpath("/activity:object/atom:content"));
	if (!$body) {
		$body = $entry->xpath("atom:content");
		if (is_array($body))
			$body = @current($body);
		if ($body)
			$body = $body->asXML();
	}


	if ($entity) {
		error_log("federated_objects_create_note:exists!");
		$note = $entity;
	}
	else {
		$guid = thewire_save_post($body, $owner->guid, $access_id, $parent_guid, $method);
		$note = get_entity($guid);
		$note->atom_id = $params['id'];
		$note->foreign = true;
	}
	return $note;
}


function federated_objects_init() {
	#elgg_register_library('elgg:push', elgg_get_plugins_path() . 'elgg-push/lib/push.php');

	elgg_register_plugin_hook_handler('push:notification', 'atom', 'federated_objects_notification');

	// callbacks for specific actions
	elgg_register_plugin_hook_handler('federated_objects:post', 'article', 'federated_objects_action_post_article');
	elgg_register_plugin_hook_handler('federated_objects:post', 'bookmark', 'federated_objects_action_post_article');
	elgg_register_plugin_hook_handler('federated_objects:post', 'note', 'federated_objects_action_post_note');
	elgg_register_plugin_hook_handler('federated_objects:join', 'group', 'federated_objects_action_post_article');
	FederatedObject::register_constructor('person', 'federated_objects_create_person');
	FederatedObject::register_constructor('note', 'federated_objects_create_note');
}

elgg_register_event_handler('init', 'system', 'federated_objects_init');
