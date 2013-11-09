<?php
/**
 * The AS JSON view for an image album
 *
 * @uses $vars['entity'] The album entity
 */

$album = elgg_extract('entity', $vars);

$obj_vars = array(
	'display_name' => $album->getTitle(),
	'published' => ActivityStreams::formatDate($album->time_created),
	'summary' => $album->description,
	'updated' => ActivityStreams::formatDate($album->time_updated),
	'object_url' => $album->getURL(),
	'total_items' => $album->getSize()
);

// the object view has its own logic for icons
$ci = $album->getCoverImageGUID();
$ci_entity = get_entity($ci);
if ($ci_entity) {
	$obj_vars['image'] = elgg_view_entity_icon($ci_entity);
}


echo elgg_view('activity_streams/object/collection', $obj_vars);