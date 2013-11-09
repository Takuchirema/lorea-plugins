<?php
/**
 * The base class for an activity stream JSON object.
 * Other objects accept more or require different attributes.
 *
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#object-types
 *
 * @uses AS/Collection $vars['attachments']           JSON-encoded array of other AS objects.
 * @uses AS/Person     $vars['author']                An AS 'person' json object idenfitying the creator of the object.
 * @uses string        $vars['content']               Natural langauge. HTML ok.
 * @uses string        $vars['display_name']          Natural language name of the object. No HTML.
 * @uses array         $vars['downstream_duplicates'] JSON-encoded array of IRIs that duplicate this object's content.
 * @uses string        $vars['id']                    The absolute, permanent IRI of the object.
 * @uses AS/MediaLink  $vars['image']                 A visual representation of the object
 * @uses string        $vars['type']                  A valid AS object type.
 * @uses string        $vars['published']             The RFC3339 date-time when the object was published.
 * @uses string        $vars['summary']               Natural language summary of the object. HTML ok.
 * @uses string        $vars['updated']               The RFC3339 date-time when the object was updated.
 * @uses array         $vars['upstream_duplicates']   JSON-encoded array of IRIs whose content this object duplicates.
 * @uses string        $vars['object_url']            An IRI to the HTML representation of the object. Can be non-unique.
 */

// base properties
$map = array(
	'attachments',
	'author',
	'content',
	'display_name' => 'displayName',
	'downstream_duplicates' => 'downstreamDuplicates',
	'id',
	'image',
	'type',
	'published',
	'summary',
	'updated',
	'upstream_duplicates' => 'upstreamDuplicates',
	'object_url' => 'url'
);

// if passed an ElggEntity use it to populate the properties.
$entity = elgg_extract('entity', $vars);
if ($entity) {
	if (!isset($vars['author']) && $vars['type'] != 'person') {
		$vars['author'] = elgg_view_entity($entity->getOwnerEntity());
	}

	$dn = $entity->title ? $entity->title : $entity->name;
	if (!isset($vars['display_name']) && $dn) {
		$vars['display_name'] = $dn;
	}

	if (!isset($vars['content']) && $entity->description) {
		$vars['content'] = $entity->description;
	}

	if (!isset($vars['id'])) {
		$vars['object_url'] = $entity->getURL();
	}

	if (!isset($vars['image'])) {
		$vars['image'] = elgg_view_entity_icon($entity);
	}

	if (!isset($vars['published'])) {
		$vars['published'] = ActivityStreams::formatDate($entity->time_created);
	}

	if (!isset($vars['summary']) && $entity->title) {
		$vars['summary'] = $entity->title;
	}

	if (!isset($vars['updated'])) {
		$vars['updated'] = ActivityStreams::formatDate($entity->time_updated);
	}

	if (!isset($vars['object_url'])) {
		$vars['object_url'] = $entity->getURL();
	}
}

$object = activity_streams_build_array($map, $vars);

// pull in object-specific properties
$object = array_merge($object, elgg_extract('properties', $vars, array()));

echo activity_streams_json_encode($object, false);