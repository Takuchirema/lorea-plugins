<?php
	gatekeeper();
	$assign_to = get_input('assign_to');
	$guid = get_input('guid');
	$user = get_entity($assign_to);
	// XXX check permissions
	if ($entity = get_entity($guid) && $user && $user->canEdit()) {
		$user->microtheme = $guid;
	}
	else {
		register_error(elgg_echo("microthemes:delete:failed"));
	}
	
	forward(REFERRER);

?>
