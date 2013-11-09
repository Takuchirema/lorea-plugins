<?php
/**
 * AS Bookmark
 *
 * @uses string $vars['target_url'] The URL the bookmark points to.
 */

$vars['type'] = 'bookmark';
$vars['properties'] = activity_streams_build_array(array('target_url' => 'targetUrl'), $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);