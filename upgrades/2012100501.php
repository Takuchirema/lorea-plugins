<?php

global $MIGRATED;

$local_version = (int)elgg_get_plugin_setting('version', 'group_alias'); 
if (2012022501 <= $local_version) { 
        error_log("Group alias requires no upgrade"); 
        // no upgrade required 
        return; 
}

elgg_load_library("elgg:group_alias");

function group_alias_filtername($name) {
        $group_name = str_replace(' ','-' ,$name);
        $group_name = str_replace(']','' ,$group_name);
        $group_name = str_replace('[','' ,$group_name);
        $group_name = strtolower($group_name);
        return $group_name;
}
function group_alias_get_groupmail($group) {
        $group_name = group_alias_filtername($group->name);
        $group_email = $group_name;
        $parent = get_entity($group->container_guid);
        if ($parent instanceof ElggGroup)
                $group_email = group_alias_filtername($parent->name)."+".$group_email;
        return $group_email;
}


function group_alias_find_alias($group) {
	return group_alias_get_groupmail($group);
}

/**
 * Save previous pad name, adapt subtype, river, clean up.
 */
function group_alias_2012100501($group) {
	require_once(elgg_get_plugins_path() . 'upgrade-tools/lib/upgrade_tools.php');
	if (empty($group->alias)) {
	//if (true) {
		$alias = group_alias_find_alias($group);
		$ngroups = elgg_get_entities_from_metadata(array(
	                'type' => 'group',
	                'metadata_name' => 'alias',
	                'metadata_value' => $alias,
        	        'limit' => 0,
        	        'count' => true,
		        ));
		try {
			$validates = group_alias_validate($alias);
		} catch (RegistrationException $e) {
			$validates = false;
		}
		if (!$validates || $ngroups > 1 || ($ngroups == 1 && $alias != $group->alias)) {
			$alias = elgg_get_friendly_title($group->name);
			$alias = preg_replace("/-/", "_", $alias);
			$alias = urldecode($alias);
			$g = get_group_from_group_alias($alias);
			if (elgg_instanceof($g, 'group') && $g->getGUID() != $group->guid){
				$alias .= $group->guid;
			}

		}
		if ($group->alias != $alias) {
			$group->alias = $alias;
		}
	}
	return true;
}

/*
 * Run upgrade. First users, then pads
 */
// users
$options = array('type' => 'group', 'limit' => 0);

$MIGRATED = 0;

$previous_access = elgg_set_ignore_access(true);
$batch = new ElggBatch('elgg_get_entities', $options, "group_alias_2012100501", 100);
elgg_set_ignore_access($previous_access);

if ($batch->callbackResult) {
	error_log("Group alias upgrade (201210050) succeeded");
	elgg_set_plugin_setting('version', 2012022501, 'group_alias');
} else {
	error_log("Group alias upgrade (201210050) failed");
}


