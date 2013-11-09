<?php
/**
 * AS Question
 *
 * @uses array $vars['options'] An array of AS objects that are possible answers to the question.
 */

$vars['type'] = 'question';

$map = array(
	'options'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);