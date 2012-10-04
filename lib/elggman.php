<?php

function incoming_mail($sender, $list, $data, $secret) {
	require_once 'Mail/mimeDecode.php';

	// check secret
	if ($secret != elggman_apikey()) {
		error_log('incorrect api key on the mail server');
		return;
	}

	// check user and group are valid
	$group = get_group_from_group_alias($list);
	$user = current(get_user_by_email($sender));
	if (!$group || !$user) {
		error_log("no group or user for email! $user->name $group->name");
		return;
	}

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

	$message_id = htmlspecialchars_decode($result->headers['message-id']);
	$in_reply_to = htmlspecialchars_decode($result->headers['in-reply-to']);

	$message_id = trim($message_id, '<>');

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
			error_log('no parent');
			return;
		}
		$reply_guid = threads_reply($parent_guid, $body, $subject);
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
			'tags' => null );
		$topic_guid = threads_create($guid, $options);
		add_to_river('river/object/groupforumtopic/create', 'create', $user->guid, $topic_guid);
	}
}
