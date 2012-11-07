<?php
if (get_loggedin_user()) {
	if (get_context() == 'groups')
	{
	   echo elgg_view("favorites/sidebar/groups", $vars);


	}
	elseif (get_context() == 'members') {
	   echo elgg_view('favorites/sidebar/users', $vars);
	}
}
?>
