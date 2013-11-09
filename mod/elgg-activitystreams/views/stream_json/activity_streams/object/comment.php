<?php
/**
 * AS Comment
 *
 * @uses array $vars['in_reply_to'] An array of AS Objects that this comment is a reply to.
 *
 */

$vars['type'] = 'comment';
$vars['properties'] = activity_streams_build_array(array('in_reply_to' => 'inReplyTo'), $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);