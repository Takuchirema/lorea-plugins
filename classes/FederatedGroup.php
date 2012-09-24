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
			if ($owner) {
				$group->owner_guid = $owner->guid;
			}
				
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
			if ($params['tags'])
				$group->interests = $params['tags'];
			$group->atom_id = $params['id'];
			$group->atom_link = $params['link'];
			$group->foreign = true;
			$group->save();
			if ($owner) {
				add_to_river('river/group/create', 'create', $owner->guid, $group->getGUID(), $group->access_id);
				FederatedObject::search_tag_river($group, $owner, 'create', $notification);
			}
			elgg_set_ignore_access($access);
		}
		return $group;
	}
	public static function onGroupJoin($hook, $type, $return, $params) {
		global $CONFIG;
		error_log("Group Join!");
		$notification = $params['notification'];
		$entry = $params['entry'];

		$author = $notification->getAuthor();
		$object = $notification->getObject();

		if ($author['type'] != 'person' || $object['type'] != 'group')
			error_log("onGroupJoin with wrong parameters!!");

		$id = $notification->getID();
		$river_id = AtomRiverMapper::getRiverID($id);

		if ($river_id || $notification->isLocal()) {
			return;
		}

		$user = FederatedObject::create($author);

		$object['entry'] = $entry;
		$object['notification'] = $notification;
		$group = FederatedObject::create($object);

		// join or request
		$join = false;
		if ($group->isPublicMembership() || $group->canEdit($user->guid)) {
			// anyone can join public groups and admins can join any group
			$join = true;
		} else {
			if (check_entity_relationship($group->guid, 'invited', $user->guid)) {
				// user has invite to closed group
				$join = true;
			}
		}

		if ($join) {
			if (groups_join_group($group, $user)) {
				error_log("joined group!");
			} else {
			}
		} else {
			add_entity_relationship($user->guid, 'membership_request', $group->guid);
			error_log("requested membership for group!");

			// Notify group owner
			$url = "{$CONFIG->url}groups/requests/$group->guid";
			$subject = elgg_echo('groups:request:subject', array(
				$user->name,
				$group->name,
			));
			$body = elgg_echo('groups:request:body', array(
				$group->getOwnerEntity()->name,
				$user->name,
				$group->name,
				$user->getURL(),
				$url,
			));
			if (notify_user($group->owner_guid, $user->getGUID(), $subject, $body)) {
			} else {
			}
		}


		return $return;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return groups_url($object);
	}
}

