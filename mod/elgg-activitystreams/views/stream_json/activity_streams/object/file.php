<?php
/**
 * AS File
 *
 * @uses AS/Collection $vars['file_url']  URL to the file this describes
 * @uses AS/Collection $vars['mime_type'] The mime type of the object
 */

$vars['type'] = 'file';

$map = array(
	'file_url' => 'fileUrl',
	'mime_type' => 'mimeType'
);

$vars['properties'] = activity_streams_build_array($map, $vars);

echo elgg_view('activity_streams/object/elements/base', $vars);