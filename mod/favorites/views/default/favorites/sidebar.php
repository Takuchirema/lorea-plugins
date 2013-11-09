<?php
if (elgg_is_logged_in()) {
	if (elgg_get_context() == 'groups')
	{
	   echo elgg_view("favorites/sidebar/groups", $vars);


	}
	elseif (elgg_get_context() == 'members') {
	   echo elgg_view('favorites/sidebar/users', $vars);
	}
}
?>
