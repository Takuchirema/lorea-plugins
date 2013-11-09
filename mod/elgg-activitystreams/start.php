<?php
/**
 * Random thoughts
 *
 * JSON encoding can only be done one level at a time.
 *
 * The lowest level of a view chain needs to do a deep encoding
 *	This is the object/* views
 *
 * Every other level needs to do a shallow encoding.
 *
 * Entity views (<type>/<subtype>) are used for the AS objects.
 *
 * River items views in the stream_json viewtype are used for rending the AS activity object,
 * if found. If not, everything is routed through river/item.
 *
 * River item views with suffixes are / will be used for the content and title:
 * "$item->view/title" and "$item->view/content" (@todo)
 *
 * References:
 * Where these disagree I've used the JSON specs / schemas.
 * Where the JSON specs / schemas disagree I've arbitrarily picked one and hoped for the best.
 *
 *  General specs: http://activitystrea.ms/head/activity-schema.html
 *  A slightly different spec: http://activitystrea.ms/specs/json/schema/activity-schema.html
 *  JSON specs:    http://activitystrea.ms/specs/json/1.0/
 */

function activity_streams_note_parent($hook, $type, $return, $params) {
	$object = $params['entity'];
	if ($object && $object->getSubtype() == 'thewire' && $object->reply) {
		$parents = $object->getEntitiesFromRelationship('parent');
		if ($parents) {
			$parent = $parents[0];
			return $parent;
		}
	}
	elseif ($object && $object->getSubtype() == 'page') {
		$parent = get_entity($object->container_guid);
		if ($parent) {
			return $parent;
		}
	}

        return $return;
}

function activity_streams_init() {
	elgg_extend_view('page/elements/head', 'activity_streams/head_ext');

	elgg_register_plugin_hook_handler('activitystreams:parent', 'entity', 'activity_streams_parent');
}

elgg_register_event_handler('init', 'system', 'activity_streams_init');

/**
 * JSON encode data, but for objects and arrays, don't re-encode anything.
 *
 * @todo this can be simplified by just always checking the elements and never re-encoding.
 *
 * @param mixed $data      Data to encode
 * @param bool  $recursive Recursively encode arrays or objects, or just the first level?
 *                         If not recursive, then assume strings, bools, or ints
 * @param int   $options   Bitwise encoding options. @see json_encode. WARNING: ignored if not recursive.
 *
 * @return string
 */
function activity_streams_json_encode($data, $recursive = true, $options = 0) {
	// no other modifications needed.
	if ($recursive) {
		return json_encode($data, $options);
	}

	$is_array = is_array($data);
	$is_object = is_object($data);

	if (!$is_array && !$is_object) {
		return json_encode($data, $options);
	}

	if ($is_object && method_exists($data, 'jsonSerialize')) {
		// if the object defines its own json serialization method, use it.
		return json_encode($object);
	}

	// json encode only the first level of items
	$is_assoc_array = activity_streams_is_assoc($data);;
	$json = array();

	if ($is_object || $is_assoc_array) {
		foreach ($data as $k => $v) {
			// keys. don't handle exotic keys.
			// if you make objects keys you don't deserve to json encode stuff.
			if (is_string($k)) {
				$k = json_encode($k);
			}
			if (!as_is_json($v)) {
				$v = json_encode($v, $options);
			}
			
			$json[] = "$k:$v";
		}
		$json = implode(',', $json);
		return '{' . $json . '}';
	} else {
		foreach ($data as $v) {
			if (!as_is_json($v)) {
				$json[] = json_encode($v, $options);
			} else {
				$json[] = $v;
			}
		}
		$json = implode(',', $json);
		return "[$json]";
	}
}

/**
 * Return the correct quoting for a to-be-json-encoded value.
 *
 * @param mixed $v
 * @return string
 */
function activity_streams_return_json_value($v, $options = 0) {
	$is_json = json_decode($v);

	if ($is_json) {
		return $v;
	} else {
		return json_encode($v, $options);
	}
}

/**
 * Check if $array is not an array with consecutive keys starting at 0.
 *
 * @param mixed $array
 * @return boolean
 */
function activity_streams_is_assoc($array) {
	if (!is_array($array)) {
		return false;
	}
	
	return array_keys($array) !== range(0, sizeof($array) - 1);
}

/**
 * Takes an map of keys in $vars and maps them to keys in the returned array with values
 * extracted from $vars.
 *
 * Maps should looke like array('key_in_vars' => 'key_in_return');
 *
 * @param array $map
 * @param array $vars
 */
function activity_streams_build_array($map, $vars) {
	$r = array();
	foreach ($map as $vars_key => $return_key) {
		// if the vars key is int, this is a shortcut definition.
		// use the value for both keys
		if (is_numeric($vars_key)) {
			$vars_key = $return_key;
		}
		// set even if it's falsey.
		if (array_key_exists($vars_key, $vars)) {
			$r[$return_key] = $vars[$vars_key];
		}
	}

	return $r;
}

/**
 * Is this data json encoded?
 *
 * @param string $data
 * @return boolean
 */
function as_is_json($data) {
	if (json_decode($data)) {
		return true;
	}
	return false;
}

/**
 * Returns a title suitable for use as an activity title.
 *
 * This is usually some form of a river summary.
 * Checks for <$item->view>/title under the stream_json viewtype.
 * Will use river/elements/summary under the default viewtype otherwise.
 *
 * @param array $vars Vars that include ElggRiverItem $vars['item']
 * @return string
 */
function activity_streams_get_activity_title($vars) {
	$item = elgg_extract('item', $vars);

	if (!$item) {
		return false;
	}

	if (elgg_view_exists($item->view . '/title')) {
		return elgg_view($item->view . '/title', $vars);
	}

	return elgg_view('river/elements/summary', $vars, false, false, 'default');
}
