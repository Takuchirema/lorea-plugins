<?php

class FederatedFile {
	public static function create($params, $entity, $tag) {
		$owner = $params['owner_entity'];
		$entry = $params['entry'];
		$notification = $params['notification'];
		$access_id = ACCESS_PUBLIC;

		$body = $notification->getBody();
		$file_link = @current($entry->xpath("$tag/atom:link[attribute::rel='enclosure']/@href"));
		$file_mimetype = @current($entry->xpath("$tag/atom:link[attribute::rel='enclosure']/@type"));
		$preview = $params['icon'];

		if ($entity) {
			$file = $entity;
		}
		else {
			$access = elgg_set_ignore_access(true);

			$file = new FilePluginFile();
			$file->owner_guid = $owner->getGUID();
			$file->subtype = 'file';
			$file->title = $params['name'];
			$file->description = $body;
			$file->access_id = $access_id;
			$file->file_link = $file_link;

			if (isset($params['container_entity'])) {
				$file->container_guid = $params['container_entity']->getGUID();
			}
			if ($params['tags']) {
				$file->tags = $params['tags'];
			}
			
			if ($preview)
				FederatedFile::setIcon($file, $preview, $owner);
			$file->save();
			$id = add_to_river('river/object/file/create', 'create', $owner->getGUID(), $file->guid);
			AtomRiverMapper::setIDMapping($id, $notification->getID(), $notification->provenance);
			$file->atom_id = $params['id'];
			$file->atom_link = $params['link'];
			$file->foreign = true;


			elgg_set_ignore_access($access);
		}
		return $file;
	}
	public static function setIcon($file, $preview, $owner) {
		$filestorename = elgg_strtolower(time().elgg_get_friendly_title($file->title));
		$prefix = "file/";
		$file->setFilename($prefix . $filestorename);
		$data = file_get_contents($preview);
		$file->open("write");
		$file->write($data);
		$mime_type = "image/png";
		$file->setMimeType($mime_type);
		$file->close();
		$file->simpletype = "image";
		$guid = $file->save();
		if ($guid && $file->simpletype == "image") {
			$file->icontime = time();

			$thumbnail = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 60, 60, true);
			if ($thumbnail) {
				$thumb = new ElggFile();
				$thumb->owner_guid = $owner->getGUID();
				$thumb->setMimeType($mime_type);

				$thumb->setFilename($prefix."thumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumbnail);
				$thumb->close();

				$file->thumbnail = $prefix."thumb".$filestorename;
				unset($thumbnail);
			}

			$thumbsmall = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 153, 153, true);
			if ($thumbsmall) {
				$thumb->setFilename($prefix."smallthumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumbsmall);
				$thumb->close();
				$file->smallthumb = $prefix."smallthumb".$filestorename;
				unset($thumbsmall);
			}
			$thumblarge = get_resized_image_from_existing_file($file->getFilenameOnFilestore(), 600, 600, false);
			if ($thumblarge) {
				$thumb->setFilename($prefix."largethumb".$filestorename);
				$thumb->open("write");
				$thumb->write($thumblarge);
				$thumb->close();
				$file->largethumb = $prefix."largethumb".$filestorename;
				unset($thumblarge);
			}
		}

	}
	public static function url($object) {
		if ($object->atom_link)
			return $object->atom_link;
		return file_url_override($object);
	}

}

