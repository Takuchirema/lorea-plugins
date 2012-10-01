<?php

class FederatedPerson {
	public static function getPoco($notification, $tag, $args) {
		$name = $notification->xpath(array("$tag/poco:displayName"));
                $description = $notification->xpath(array("$tag/poco:note"));
                $webpage = $notification->xpath(array("$tag/poco:urls/poco:value"));
                if ($name) {
                        $args['name'] = $name;
                }
		$args['description'] = $description;
		$args['webpage'] = $webpage;
		return $args;
	}
	public static function create($params, $entity, $tag) {
		$notification = $params['notification'];
		$params = FederatedPerson::getPoco($notification, $tag, $params);
		$icon = $notification->getIcon($tag);
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
			if ($params['description']) {
				$entity->description = $params['description'];
			}
			if ($params['webpage']) {
				$entity->webpage = $params['webpage'];
			}
			$entity->access_id = ACCESS_PUBLIC;
			$entity->atom_id = $params['id'];
			$entity->atom_link = $params['link'];
			$entity->foreign = true;

			$entity->save();
			FederatedPerson::setIcon($entity, $icon);
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
	public static function setIcon($entity, $icon, $prefix='profile') {
		$guid = $entity->guid;
		$resized = file_get_contents($icon);

		// save downloaded image
		if ($entity instanceof ElggUser) 
	                $owner = $guid;
		else
	                $owner = $entity->owner_guid;

		$file = new ElggFile();
                $file->setFilename("$prefix/{$guid}.jpg");
		$file->owner_guid = $owner;
                $file->open('write');
                $file->write($resized);
                $file->close();
		$filename = $file->getFilenameOnFilestore();
		$icon_sizes = elgg_get_config('icon_sizes');

		// get the images and save their file handlers into an array
		// so we can do clean up if one fails.
		$files = array();
		foreach ($icon_sizes as $name => $size_info) {
			$resized = get_resized_image_from_existing_file($filename, $size_info['w'], $size_info['h'], $size_info['square'], 0,0,0,0,$size_info['upscale']);

			if ($resized) {
				//@todo Make these actual entities.  See exts #348.
				$file = new ElggFile();
				$file->owner_guid = $owner;
				$file->setFilename("$prefix/{$guid}{$name}.jpg");
				$file->open('write');
				$file->write($resized);
				$file->close();
				$files[] = $file;
			} else {
				// cleanup on fail
				foreach ($files as $file) {
					$file->delete();
				}
			}
		}
		$entity->x1 = 0;
		$entity->x2 = 0;
		$entity->y1 = 0;
		$entity->y2 = 0;
		$entity->icontime = time();
		if ($entity instanceof ElggUser) {
			elgg_trigger_event('profileiconupdate', $entity->type, $entity);
		}
	}

}

