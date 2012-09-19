<?php
        admin_gatekeeper();

	elgg_load_library('elgg:push:download');

	$guid = get_input('guid');
	if ($entity = get_entity($guid)) {
		$url = $entity->topic;
		elgg_download_atom_activitystreams($url, $entity);
	}

        forward("admin/administer_utilities/push");
?>
