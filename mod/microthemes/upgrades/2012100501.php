<?php

global $MIGRATED;

$options = array(
        'type' => 'object',
        'subtypes' => array('microtheme'),
        'limit' => 1,
        'metadata_name' => 'hidesitename',
        'metadata_values' => array(0, 1),
);

$topics = elgg_get_entities_from_metadata($options);

// if no microthemes with hidesitename property we're ok
if (!$topics) {
	return;
}

/**
 * upgrade function, generates thumbnails
 */
function microthemes_2012100501($microtheme) {
	$MIGRATED += 1;
	if ($MIGRATED % 100 == 0) {
		error_log(" * microtheme user $microtheme->guid");
	}

	$prefix = "microthemes/banner_".$microtheme->guid;
	$filehandler = new ElggFile();
	$filehandler->owner_guid = $microtheme->owner_guid;
	$filehandler->setFilename($prefix.'_master.jpg');
	$mime = $filehandler->getMimeType();
        try {
	if ($filehandler->exists()) {
		 microthemes_create_thumbnails($microtheme, $filehandler);
	}
	} catch (Exception $e) {
	}
	$microtheme->save();
	$microtheme->deleteMetadata('hidesitename');
	return true;
}

/*
 * Run upgrade.
 */
$options['limit'] = 0;

$previous_access = elgg_set_ignore_access(true);
$batch = new ElggBatch('elgg_get_entities_from_metadata', $options, "microthemes_2012100501", 100, false);
elgg_set_ignore_access($previous_access);

if ($batch->callbackResult) {
	error_log("Elgg microthemes upgrade (201210050) succeeded");
} else {
	error_log("Elgg microthemes upgrade (201210050) failed");
}


