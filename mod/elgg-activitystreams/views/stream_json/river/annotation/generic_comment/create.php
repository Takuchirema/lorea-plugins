<?php
/**
 * Properly format a comment for AS.
 */

$item = elgg_extract('item', $vars);
$annotation = get_annotation($item->annotation_id);

$vars['object'] = elgg_view_annotation($annotation);

echo elgg_view('activity_streams/object/activity', $vars);