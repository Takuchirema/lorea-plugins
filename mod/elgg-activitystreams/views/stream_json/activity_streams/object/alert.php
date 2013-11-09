<?php
/**
 * AS Alert
 *
 * @see activity_streams/object/elements/base view for options.
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#object-types for descriptions
 */

$vars['type'] = 'alert';
echo elgg_view('activity_streams/object/elements/base', $vars);