<?php
/**
 * A single river entry.
 */

// resolve the view here to see if we need to call an item-specific view.
// otherwise, use the default activity view

$item = elgg_extract('item', $vars);

if ($item && elgg_view_exists($item->view)) {
	echo elgg_view($item->view, $vars);
} else {
	echo elgg_view('activity_streams/object/activity', $vars);
}