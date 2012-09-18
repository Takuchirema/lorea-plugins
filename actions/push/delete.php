<?php
        admin_gatekeeper();

	$guid = get_input('guid');
	if ($entity = get_entity($guid)) {
		
		if ($entity->canEdit() || isadminloggedin()) {
			
			if ($entity->delete()) {
				
				system_message(elgg_echo("push:delete:success"));
				forward("admin/administer_utilities/push");				
				
			}
			
		}
		
	}
	
	register_error(elgg_echo("push:delete:failed"));
	forward("admin/administer_utilities/push");

?>
