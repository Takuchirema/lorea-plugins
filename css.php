<?php
	header("Content-type: text/css");
	$guid = get_input('guid');
	$owner = get_entity($guid);
	if ($guid && $owner && $owner->microtheme) {
		$microtheme = get_entity($owner->microtheme);
	$bgopts = 'top';
	if ($microtheme->bg_alignment)
		$bgopts .= ' '.$microtheme->bg_alignment;
	else
		$bgopts .= ' left';
	if ($microtheme->repeatx && $microtheme->repeaty) {
		// nothing is needed
	}
	else {
		if ($microtheme->repeatx)
			$bgopts .= ' repeat-x';
		if ($microtheme->repeaty)
			$bgopts .= ' repeat-y';
		if (!$microtheme->repeatx && !$microtheme->repeaty)
			$bgopts .= ' no-repeat';
	}
	$height = 120;
	if ($microtheme->height) {
		$height = $microtheme->height;
	}
	$hide_sitename = false;
	if ($microtheme->hidesitename)
		$hide_sitename = true;
?>
body, #page_container {
	background-color: <?php echo $microtheme->bg_color; ?>;
	background: <?php echo $microtheme->bg_color; ?> url(<?php echo $microtheme->bgurl; ?>) <?php echo $bgopts; ?>;

}

#two_column_left_sidebar {
background-color: #dedede;
background-image: url(https://n-1.artelibredigital.net/mod/theme_loreahub/graphics/contentbg.png);
}

#layout_header {
	height: <?php echo $height; ?>px;
	background: transparent;
}

<?php
	if ($hide_sitename) {
?>
#wrapper_header h1 {
	display: none;
}
<?php
	}
?>

#wrapper_header img {
	display: none;
}

#elgg_topbar {

	background: <?php echo $microtheme->topbar_color; ?> repeat-x top left;
}
/*#page_container {
	background-color: <?php echo $microtheme->bg_color; ?>;
}*/
<?php
	}
?>
