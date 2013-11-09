<?php
/**
 * AS Page
 *
 * @tip Use this for profiles
 *
 * @see activity_streams/object/elements/base view for options.
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#object-types for descriptions
 */

$vars['type'] = 'page';
echo elgg_view('activity_streams/object/elements/base', $vars);