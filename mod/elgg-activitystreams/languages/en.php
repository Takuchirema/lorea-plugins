<?php
/**
 * Valid Verbs
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#verbs
 */
$en = array(
	'activity_streams:verb:create' => 'post',
	'activity_streams:verb:comment' => 'post',
	'activity_streams:verb:update' => 'update',
	'activity_streams:verb:edited' => 'update',

	// group actions
	'activity_streams:verb:join' => 'join',
	'activity_streams:verb:leave' => 'leave',

	// messageboard
	'activity_streams:verb:messageboard' => 'post',

	// we use follow model here.
	'activity_streams:verb:friend' => 'follow',
);

add_translation('en', $en);
