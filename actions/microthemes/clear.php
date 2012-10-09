<?php
	gatekeeper();
	$assign_to = get_input('assign_to');
	$user = get_entity($assign_to);
	// XXX check permissions
	if ($user && $user->canEdit()) {
		$user->deleteMetadata('microtheme');
	}
	else {
		register_error(elgg_echo("microthemes:clear:failed"));
	}
	
	forward(REFERRER);

?>
