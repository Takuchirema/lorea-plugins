<?php

global $CONFIG;

if (!elgg_is_logged_in()) {
	exit();
}

$q = sanitise_string(get_input('term'));

// replace mysql vars with escaped strings
$q = str_replace(array('_', '%'), array('\_', '\%'), $q);

$user_guid = elgg_get_logged_in_user_guid();
$entities = elgg_get_entities(array(
	'type' => 'group',
	'joins' => array(
		"NATURAL JOIN {$CONFIG->dbprefix}groups_entity ge",
		", {$CONFIG->dbprefix}entity_relationships er",
	),
	'wheres' => array(
		"(ge.name LIKE '$q%' OR ge.name LIKE '% $q%')",
		"((e.owner_guid = $user_guid) OR (
			er.relationship = 'operator'
			AND er.guid_one = $user_guid
			AND er.guid_two = ge.guid))",
	),
	'limit' => 40,
));


$results = array();
foreach ($entities as $entity) {
	$entity = get_entity($entity->guid);
	if (!$entity) {
		continue;
	}

	$output = elgg_view_list_item($entity, array(
		'use_hover' => false,
		'class' => 'elgg-autocomplete-item',
	));

	$icon = elgg_view_entity_icon($entity, 'tiny', array(
		'use_hover' => false,
	));
	$results[$entity->name . rand(1, 100)] = array(
		'type' => 'group',
		'name' => $entity->name,
		'desc' => strip_tags($entity->description),
		'guid' => $entity->guid,
		'label' => $output,
		'value' => $entity->guid,
		'icon' => $icon,
		'url' => $entity->getURL(),
	);
}

ksort($results);
header("Content-Type: application/json");
echo json_encode(array_values($results));
exit;
