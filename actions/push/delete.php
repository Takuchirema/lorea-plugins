<?php
        admin_gatekeeper();

	elgg_load_library('elgg:push');

	$guid = get_input('guid');
	if ($entity = get_entity($guid)) {
		
		if ($entity->canEdit() || isadminloggedin()) {
			
			if (push_unsubscribeto($entity->topic) && $entity->delete()) {
				
				system_message(elgg_echo("push:delete:success"));
				forward("admin/administer_utilities/push");				
				
			}
			
		}
		
	}
	
	register_error(elgg_echo("push:delete:failed"));
	forward("admin/administer_utilities/push");

?>
