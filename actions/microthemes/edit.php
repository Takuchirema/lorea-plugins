<?php
	$entity_guid = get_input('entity_guid');
	$title = get_input('title');
	$assign_to = get_input('assign_to');
	$height = get_input('height');
	try {
		$height = (int)$height;
	}
	catch (Except $e) {
		$height = 120;
	}
	$bg_color = get_input('bg_color');
	$repeat = get_input('repeat');
	$options = get_input('options');
	$alignment = get_input('alignment');
	$topbar_color = get_input('topbar_color');
	$banner_file = get_input('banner_file');
	$footer_file = get_input('footer_file');

	if ($entity_guid) {
		$newObject = new ElggObject($entity_guid);
		$newObject->title = $title;
	}
	else {
		$newObject = new ElggObject();
		$newObject->title = $title;
		$newObject->subtype = 'microtheme';
		$newObject->owner_guid = get_loggedin_userid();
		$newObject->container_guid = get_loggedin_userid();
		$newObject->access_id = ACCESS_PUBLIC;
	}
	if ($newObject->save()) {
		error_log("created ok");
		$newObject->bg_alignment = $alignment;
		if ($options && in_array('hidesitename', $options))
			$newObject->hidesitename = 1;
		else
			$newObject->hidesitename = 0;
		if ($repeat && in_array('repeatx', $repeat))
			$newObject->repeatx = 1;
		else
			$newObject->repeatx = 0;
		if ($repeat && in_array('repeaty', $repeat))
			$newObject->repeaty = 1;
		else
			$newObject->repeaty = 0;
		$newObject->bg_color = $bg_color;
		$newObject->height = $height;
		$newObject->topbar_color = $topbar_color;
		// save the banner
		if ((isset($_FILES['banner_file'])) && (substr_count($_FILES['banner_file']['type'],'image/')))
                {
			$prefix = "microthemes/banner_".$newObject->guid;
			$filehandler = new ElggFile();
			$filehandler->owner_guid = $newObject->owner_guid;
			$filehandler->container_guid = $newObject->owner_guid;
			$filehandler->setFilename($prefix.'_master.jpg');
			$filehandler->open("write");
			$filehandler->write(get_uploaded_file('banner_file'));
			$filehandler->close();
			$newObject->bgurl = $CONFIG->wwwroot.'mod/microthemes/graphics/icon.php?size=master&mode=banner&object_guid='.$newObject->guid;
			// thumbnails
			$thumbsmall = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),40,40, true);
			$thumbmedium = get_resized_image_from_existing_file($filehandler->getFilenameOnFilestore(),100,100, false);
			if ($thumbsmall) {
				$thumb = new ElggFile();
				$thumb->owner_guid = $newObject->owner_guid;
				$thumb->container_guid = $newObject->owner_guid;
				$thumb->setMimeType('image/jpeg');

				$thumb->setFilename($prefix."_small.jpg");
                                $thumb->open("write");
                                $thumb->write($thumbsmall);
                                $thumb->close();

				$thumb->setFilename($prefix."_medium.jpg");
                                $thumb->open("write");
                                $thumb->write($thumbmedium);
                                $thumb->close();
			}

		}
		// save the footer
		/*if ((isset($_FILES['footer_file'])) && (substr_count($_FILES['footer_file']['type'],'image/')))
                {
		}*/
	}
	forward("pg/microthemes/view?assign_to=".$assign_to);
?>
