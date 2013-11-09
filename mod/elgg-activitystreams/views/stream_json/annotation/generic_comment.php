<?php

$annotation = elgg_extract('annotation', $vars);
$entity = $annotation->getEntity();

$vars['author'] = elgg_view_entity($annotation->getOwnerEntity());
$vars['in_reply_to'] = elgg_view_entity($entity);
$vars['content'] = $annotation->value;

echo elgg_view('activity_streams/object/comment', $vars);