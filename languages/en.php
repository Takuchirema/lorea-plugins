<?php
/**
 * Valid Verbs
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#verbs
 */
$en = array(
        'ostatus:subscribeto:enter' => 'Enter an address to subscribe to',
        'ostatus:subscribeto:enter:description' => 'Enter the profile page you want to subscribe to, or an ostatus address like delirium@identi.ca',
        'ostatus:subscribeto' => 'Do you want to subscribe to the following person?',
	// follow / unfollow
        'activity_streams:verb:follow' => 'http://activitystrea.ms/schema/1.0/follow',
        'activity_streams:verb:unfollow' => 'http://ostatus.org/schema/1.0/unfollow',

);

add_translation('en', $en);
