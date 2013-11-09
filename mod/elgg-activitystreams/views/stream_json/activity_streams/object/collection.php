<?php
/**
 * The basis for an AS JSON document.
 *
 * @see http://activitystrea.ms/specs/json/1.0/#collection
 *
 * Mandatory
 * @uses AS/collection $vars['items'] JSON-encoded array of items
 *
 * Optional
 * @uses int    $vars['total_items']    The total number of items in the list
 * @uses string $vars['collection_url'] IRI to a JSON document of the full collection. NOTE THE NAME.
 * @uses array  $vars['object_types']   An array of object types in the items.
 */

$vars['type'] = 'collection';

$map = array(
	'items' => 'items',
	'total_items' => 'totalItems',
	'collection_url' => 'url',
	'object_types' => 'objectTypes'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);
