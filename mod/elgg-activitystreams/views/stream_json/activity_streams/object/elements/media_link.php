<?php
/**
 * AS media link
 *
 * @see http://activitystrea.ms/specs/json/1.0/#media-link
 *
 * @uses ElggEntity $vars['entity'] If passed an entity, will use the getIcon() method.
 *
 * Mandatory
 * @uses string $vars['media_url'] NOTE THE NAME. As of 1.8 elgg_view() clobbers the $vars['url']
 *
 * Optional
 * @uses int $vars['duration'] Length in seconds
 * @uses int $vars['height']   Height in pixels
 * @uses int $vars['width']    Width in pixels
 */

$map = array(
	'media_url' => 'url',
	'duration',
	'height',
	'width'
);

$ml = activity_streams_build_array($map, $vars);

echo activity_streams_json_encode($ml);