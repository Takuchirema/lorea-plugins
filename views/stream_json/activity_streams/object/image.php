<?php
/**
 * AS Image
 *
 * @note There is no height and width in the spec for image. Only in the media link for the
 * full sized version.
 *
 * @uses int    $vars['height']            Height in pixels
 * @uses int    $vars['width']             Width in pixels
 * @uses string $vars['full_image_url']    A URL to the full image.
 * @uses int    $vars['full_image_height'] Height of the full sized image in pixels
 * @uses int    $vars['full_image_width']  Width of the full sized image in pixels
 */

$vars['type'] = 'image';

if ($vars['full_image_url']) {
	$map = array(
		'full_image_url' => 'media_url',
		'full_image_height' => 'height',
		'full_image_width' => 'width'
	);

	$ml_vars = activity_streams_build_array($map, $vars);
	$ml = elgg_view('activity_streams/object/elements/media_link', $ml_vars);
	$vars['properties']['fullImage'] = $ml;
}

echo elgg_view('activity_streams/object/elements/base', $vars);