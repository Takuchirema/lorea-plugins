<?php

class FederatedPerson {
	public static function create($params, $entity, $tag) {
		if ($entity) {
			if ($entity->foreign) {
				$access = elgg_set_ignore_access(true);
				$entity->atom_id = $params['id'];
				$entity->atom_link = $params['link'];
				elgg_set_ignore_access($access);
			}
		}
		else {
			$access = elgg_set_ignore_access(true);
			$entity = new ElggUser();
			$entity->owner_guid = 0;
			$entity->container_guid = 0;
			$entity->subtype = 'ostatus';
			$entity->username = FederatedPerson::randomString(8);
			$entity->save();
			$entity->username = 'ostatus_'.$entity->getGUID();
			$entity->name = $params['name'];
			$entity->access_id = ACCESS_PUBLIC;
			$entity->atom_id = $params['id'];
			$entity->atom_link = $params['link'];
			$entity->foreign = true;
			$entity->save();
			elgg_set_ignore_access($access);
		}
		return $entity;
	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return profile_url($object);
	}
	public static function randomString($length)
	{
	    // Generate random 32 charecter string
	    $string = md5(time());

	    // Position Limiting
	    $highest_startpoint = 32-$length;

	    // Take a random starting point in the randomly
	    // Generated String, not going any higher then $highest_startpoint
	    $randomString = substr($string,rand(0,$highest_startpoint),$length);

	    return $randomString;

	}
}

