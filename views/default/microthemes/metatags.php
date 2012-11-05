<?php	
$page_owner = elgg_get_page_owner_entity();
$microtheme_guid = get_input('microtheme_guid');

if ($microtheme_guid || 
       (($page_owner instanceof ElggUser &&
	 in_array($context, array("profile", "microthemes", "dashboard"))) ||
            ($page_owner instanceof ElggGroup)) && $page_owner->microtheme && get_entity($page_owner->microtheme)) {
}

	$context = get_context();
	if (!$microtheme_guid) {
		$owner = null;
		if ($context == 'activity' && elgg_get_logged_in_user_entity()) {
			$owner = elgg_get_logged_in_user_entity();
		}
		if ((($page_owner instanceof ElggUser && ($context == "profile" || $context == "microthemes" || $context == "dashboard")) ||
		    ($page_owner instanceof ElggGroup)) && $page_owner->microtheme && get_entity($page_owner->microtheme)) {
			$owner = $page_owner;
		}
		else if ($context == "microthemes") {
			$owner = get_entity(get_input("assign_to"));
			if (!$owner) {
				$user = get_loggedin_user();
				if ($user->microtheme && get_entity($user->microtheme))
					$owner = $user;
			}
		}
		$microtheme_guid = $owner->microtheme;
	}
        $microtheme = get_entity($microtheme_guid);
        if ($owner && $microtheme) {
                $last_update = $microtheme->time_updated;

?>
        <link rel="stylesheet" href="<?php echo $vars['url']; ?>microthemes/css/<?php echo $microtheme_guid; ?>&last_update=<?php echo $last_update; ?>" type="text/css" />
<?php
        }

