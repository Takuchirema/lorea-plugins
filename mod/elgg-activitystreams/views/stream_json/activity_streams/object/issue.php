<?php
/**
 * AS Issue
 *
 * @uses array $vars['types'] An array of one or more absolute IRI's that describe the type of issue represented by the object.
 */

$vars['type'] = 'issue';

$map = array(
	'types'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);