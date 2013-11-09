<?php
/**
 * AS Audio
 *
 * Mandatory
 * @uses string $vars['media_url'] NOTE THE NAME.
 *
 * Optional
 * @uses string $vars['embed_code'] HTML code to embed the audio in another page.
 * @uses int    $vars['duration']   Length in seconds
 * @uses int    $vars['height']     Height in pixels
 * @uses int    $vars['width']      Width in pixels
 */

$vars['type'] = 'audio';
$vars['properties'] = activity_streams_build_array(array('embed_code' => 'embedCode'), $vars);

$ml = elgg_view('activity_streams/object/elements/media_link', $vars);
$vars['properties']['stream'] = $ml;

echo elgg_view('activity_streams/object/elements/base', $vars);