<?php

class FederatedGroup {
	public static function create($params, $entity) {
		global $CONFIG;
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$brief_description = @current($entry->xpath("activity:target/atom:summary"));
		$description = @current($entry->xpath("activity:target/atom:content"));
		if ($entity) {
			if ($entity->foreign) {
				$access = elgg_set_ignore_access(true);
				$entity->atom_id = $params['id'];
				$entity->atom_link = $params['link'];
				elgg_set_ignore_access($access);
			}
			$group = $entity;
		}
		else {
			$access = elgg_set_ignore_access(true);
			$group = new ElggGroup();
			$group->owner_guid = 0;
			$group->container_guid = 0;
			$group->subtype = 'ostatus';
			$group->name = $params['name'];
			// Set group tool options
			if (isset($CONFIG->group_tool_options)) {
				foreach ($CONFIG->group_tool_options as $group_option) {
					$group_option_toggle_name = $group_option->name . "_enable";
					if ($group_option->default_on) {
						$group_option_default_value = 'yes';
					} else {
						$group_option_default_value = 'no';
					}
					$group->$group_option_toggle_name = 'no';
					//$group->$group_option_toggle_name = get_input($group_option_toggle_name, $group_option_default_value);
				}
			}

			$group->access_id = ACCESS_PUBLIC;
			$group->membership = ACCESS_PUBLIC; // XXX
			$group->description = $description;
			$group->briefdescription = $brief_description;
			$group->atom_id = $params['id'];
			$group->atom_link = $params['link'];
			$group->foreign = true;
			$group->save();
			elgg_set_ignore_access($access);
		}
		return $group;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return groups_url($object);
	}
}

