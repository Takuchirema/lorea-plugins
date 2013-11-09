<?php
/**
 * AS Task
 *
 * Example: Write a blog post about Elgg speed improvements.
 *
 * actor = Brett
 * object = {type: 'blog', title: "Elgg Speed Improvements"}
 * verb = post
 *
 * @uses AS/Object $vars['actor']  The actor expected to complete the task.
 * @uses string    $vars['by']     RFC3339 date-time for the task deadline.
 * @uses AS/Object $vars['object'] An object describing the task.
 * @uses array     $vars['prerequisites'] Other task objects that must be completed first.
 * @uses bool      $vars['required'] Is this mandatory?
 * @uses array     $vars['supersedes'] Other task objects this task supersedes.
 * @uses string    $vars['verb'] The verb for this task.
 *
 */

$vars['type'] = 'task';

$map = array(
	'actor',
	'by',
	'object',
	'prerequisites',
	'required',
	'supersedes',
	'verb'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);