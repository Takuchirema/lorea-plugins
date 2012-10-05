<?php

function elggman_email_in_filter($entity, $email, $filter) {
	
	$options = array('guid' => $entity->guid,
		'annotation_name' => $filter,
		'annotation_value' => $email,
		);
	$access = elgg_set_ignore_access(true);
	$annotations = elgg_get_annotations($options);
	elgg_set_ignore_access($access);
	if (count($annotations)) {
		return true;
	}
	return false;
}

function elggman_message_accept($entity) {
	$group = $entity->getContainerEntity();
	$group_alias = $group->alias;
	if (elggman_incoming_mail($entity->sender, $group_alias, $entity->data, elggman_apikey(), true)) {
		$entity->delete();
	}
}

function elggman_moderated_message($group, $headers, $subject, $body, $data, $sender) {
	$in_blacklist = elggman_email_in_filter($group, $sender, 'blacklist');
	if ($in_blacklist) {
		// drop
		return;
	}
	$entity = new ElggObject();
	$entity->subtype = 'moderated_discussion';
	$user = current(get_user_by_email($sender));
	if ($user instanceof ElggUser) {
		$owner = $user;
	} else {
		$owner = $group->getOwnerEntity();
	}
	login($owner);
	$entity->owner_guid = $owner->guid;
	$entity->container_guid = $group->guid;
	$entity->access_id = $group->group_acl;

	$entity->title = $subject;
	$entity->description = $body;
	$entity->data = $data;
	$entity->sender = $sender;
	$entity->save();
	$in_whitelist = elggman_email_in_filter($group, $sender, 'whitelist');
	if ($in_whitelist) {
		elggman_message_accept($entity);
	}
}

function elggman_incoming_mail($sender, $list, $data, $secret, $accepted=false) {
	require_once 'Mail/mimeDecode.php';

	// check secret
	if ($secret != elggman_apikey()) {
		error_log('elggman: incorrect api key on the mail server');
		return;
	}

	// check user and group are valid
	$group = get_group_from_group_alias($list);
	$user = current(get_user_by_email($sender));
	if (!$group) {
		error_log("elggman: no group or user for email! $user->name $group->name $sender");
		return;
	}
	// check for moderation
	elgg_load_library('elgg:threads');

	// decode email
	$params['include_bodies'] = true;
	$params['decode_bodies']  = true;
	$params['decode_headers'] = true;

	$decoder = new Mail_mimeDecode($data);
	$result = $decoder->decode($params);

	// get message parameters
	$subject = htmlspecialchars_decode($result->headers['subject']);
	$body = htmlspecialchars_decode($result->body);
	if ((!$user || !$group->isMember($user)) && !$accepted) {
		elggman_moderated_message($group, $headers, $subject, $body, $data, $sender);
		return;
	}

	// if there is no user means this has been accepted by moderators
	if (!$user) {
		$user = $group->getOwnerEntity();
		$sender_parts = explode('@', $sender);
		$forwarded = $sender_parts[0] . '@...';
		$body .= "\n\n(".elgg_echo('elggman:forwarded', array($forwarded)).')';
		$forwarded_for = $sender;
	}

	$message_id = htmlspecialchars_decode($result->headers['message-id']);
	$in_reply_to = htmlspecialchars_decode($result->headers['in-reply-to']);

	$message_id = trim($message_id, ' <>');
	$in_reply_to = trim($in_reply_to, ' <>');

	login($user);
	if ($in_reply_to) {
		$group_mailinglist = elggman_get_group_mailinglist($group);
		if (strpos($in_reply_to, $group_mailinglist)) {
			$parent_guid = current(explode('.', $in_reply_to));
		} else {
			$parent = current(elgg_get_entities_from_metadata(array(
				'type' => 'object',
				'subtypes' => array('groupforumtopic', 'topicreply'),
				'metadata_name' => 'message_id',
				'metadata_value' => $in_reply_to,
				'limit' => 1,
			)));
			$parent_guid = $parent->guid;
		}
		if (!$parent_guid) {
			error_log("elggman: cant find parent $in_reply_to");
			return;
		}


		$reply_guid = threads_reply($parent_guid, $body, $subject, array('forwarded_for' => $forwarded_for));
		$reply = get_entity($reply_guid);
		$reply->message_id = $message_id;
		add_to_river('river/annotation/group_topic_post/reply', 'reply', $user->guid, $topic->guid, "", 0, $reply_guid);
	}
	else {
		$options = array(
			'title' => $subject,
			'description' => $body,
			'status' => 'open',
			'access_id' => $group->access_id,
			'container_guid' => $group->guid,
			'message_id' => $message_id,
			'forwarded_for' => $forwarded_for,
			'tags' => null );

		$topic_guid = threads_create($guid, $options);
		add_to_river('river/object/groupforumtopic/create', 'create', $user->guid, $topic_guid);
	}
	return true;
}
