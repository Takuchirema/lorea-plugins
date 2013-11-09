<?php
/**
 * View a list of items in JSON
 *
 * @uses $vars['items']       Array of ElggEntity or ElggAnnotation objects
 * @uses $vars['offset']      Index of the first list item in complete list
 * @uses $vars['limit']       Number of items per page. Only used as input to pagination.
 * @uses $vars['count']       Number of items in the complete list
 * @uses $vars['full_view']   Show the full view of the items (default: false)
 */

$items = $vars['items'];
$offset = elgg_extract('offset', $vars);
$limit = elgg_extract('limit', $vars);
$count = elgg_extract('count', $vars);

$json = array();

if (is_array($items) && count($items) > 0) {
	foreach ($items as $item) {
		// check if things are disabled if this is a river item
		if ($item instanceof ElggRiverItem) {
			if (!$item->getObjectEntity() || !$item->getSubjectEntity()) {
				continue;
			}
		}

		$json[] = elgg_view_list_item($item, $vars);
	}
}

echo elgg_view('activity_streams/object/collection', array(
	'items' => activity_streams_json_encode($json, false),
	'total_items' => count($json),
	'collection_url' => elgg_http_remove_url_query_element(current_page_url(), 'view')
));