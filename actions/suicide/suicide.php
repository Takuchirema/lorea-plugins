<?php
/**
 * Digital suicide action
 *
 * @package ElggSuicide
 */

$me = elgg_get_logged_in_user_entity();

$myobjects = elgg_get_entities(array(
	'owner_guid' => $me->guid,
	'limit' => 0,
));

$friends = elgg_get_entities_from_relationship(array(
	'relationship' => 'friend',
	'relationship_guid' => $guid,
	'types' => 'user',
	'subtypes' => $subtype,
	'limit' => $limit,
	'offset' => $offset,
	'count' => true,
));

$annotation_id = create_annotation(elgg_get_config('site_guid'), 'suicide', $me->name.",".$friends, 'text', elgg_get_config('site_guid'), get_default_access($me));
add_to_river('river/suicide', 'suicide', elgg_get_config('site_guid'), $CONFIG->site_guid, ACCESS_LOGGED_IN, 0, $annotation_id);

if ($me->delete()) {
	system_message(elgg_echo('suicide:success'));
} else {
	register_error(elgg_echo('suicide:fail'));
}

forward();
