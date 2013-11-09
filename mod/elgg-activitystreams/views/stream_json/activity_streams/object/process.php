<?php
/**
 * AS Process
 *
 * Represents any form of process. For instance, a long-running task that is started and
 * expected to continue operating for a period of time.
 *
 * @see activity_streams/object/elements/base view for options.
 * @see http://activitystrea.ms/specs/json/schema/activity-schema.html#object-types for descriptions
 */

$vars['type'] = 'process';
echo elgg_view('activity_streams/object/elements/base', $vars);