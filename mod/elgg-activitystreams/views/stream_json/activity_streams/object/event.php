<?php
/**
 * AS Event
 *
 * @uses AS/Collection $vars['attened_by']      Collection of AS Objects that attended the event
 * @uses AS/Collection $vars['attending']       Collection of AS Object that intend to attend the event
 * @uses string        $vars['end_time']        RFC 3339 date-time when the event ends
 * @uses AS/Collection $vars['invited']         Collection of AS Object that are invited
 * @uses AS/Collection $vars['maybe_attending'] Collection of AS Object that are invited
 * @uses AS/Collection $vars['not_attended_by'] Collection of AS Object that are invited
 * @uses AS/Collection $vars['not_attending']   Collection of AS Object that are invited
 * @uses string        $vars['start_time']      RFC 3339 date-time when the event starts
 */

$vars['type'] = 'event';

$map = array(
	'attended_by' => 'attendedBy',
	'attending',
	'end_time' => 'endTime',
	'invited',
	'maybe_attending' => 'maybeAttending',
	'not_attended_by' => 'notAttendedBy',
	'not_attending' => 'notAttending',
	'start_time' => 'startTime'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);