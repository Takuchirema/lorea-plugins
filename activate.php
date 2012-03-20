<?php
/**
 * Group Alias activation script
 * It sets an alias if group hasn't.
 */

foreach(elgg_get_entities(array('type' => 'group')) as $group){
	group_alias_update_from_name($group);
}
