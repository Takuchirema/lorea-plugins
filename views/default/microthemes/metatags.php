<?php
	global $CONFIG;
	$page_owner = page_owner_entity();
	$owner = null;
	if ((($page_owner instanceof ElggUser && (get_context() == "profile" || get_context() == "microthemes" || get_context() == "dashboard")) || 
	    ($page_owner instanceof ElggGroup)) && $page_owner->microtheme) {
		$owner = $page_owner;
	}
	else if (get_context() == "microthemes") {
		$owner = get_entity(get_input("assign_to"));
		if (!$owner) {
			$user = get_loggedin_user();
			if ($user->microtheme)
				$owner = get_loggedin_user();
		}
	}
	if (!$owner) {
		$site = get_site_by_url($CONFIG->wwwroot);
		$owner = $site;
	}
	if ($owner && $owner->microtheme) {
?>
	<link rel="stylesheet" href="<?php echo $vars['url']; ?>pg/microthemes/css&guid=<?php echo $owner->getGUID(); ?>" type="text/css" />
<?php
	}
?>
