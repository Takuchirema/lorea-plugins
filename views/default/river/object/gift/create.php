<?php
/**
 * Gift river view.
 */

$object = $vars['item']->getObjectEntity();

echo elgg_view('river/elements/layout', array(
	'item' => $vars['item'],
	'summary' => elgg_view('river/object/gift/summary', $vars),
	'message' => $object->title,
));
