<?php
/**
 * Resets container guids from all groups that aren't subgroups to owner guid
 */

$groups = elgg_get_entities(array(
	'type' => 'group',
	'limit' => 0,
));

foreach($groups as $group) {
	if (!elgg_instanceof($group->getContainerEntity(), 'group')) {
		$group->container_guid = $group->owner_guid;
		$group->save();
	}
}
