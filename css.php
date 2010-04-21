<?php
	global $CONFIG;
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
	$translucid_page = false;
	if ($microtheme->translucid_page)
		$translucid_page = true;


?>

.colorSelector {
	position: relative;
	width: 36px;
	height: 36px;
	background: url(<?php echo $CONFIG->wwwroot; ?>mod/microthemes/vendors/images/select.png);
}
.colorSelector div {
	position: absolute;
	top: 3px;
	left: 3px;
	width: 30px;
	height: 30px;
	background: url(<?php echo $CONFIG->wwwroot; ?>mod/microthemes/vendors/images/select.png) center;
}

body, #page_container {
	background-color: <?php echo $microtheme->bg_color; ?>;
	background: <?php echo $microtheme->bg_color; ?> url(<?php echo $microtheme->bgurl; ?>) <?php echo $bgopts; ?>;

}




<?php
	if ($translucid_page) {
?>
#layout_canvas {
	background: transparent;
	background-image: url(<?php echo $CONFIG->wwwroot; ?>mod/microthemes/graphics/contentbg.png);
	border-color: #cccccc;
	border-width-rtl-source: physical;
	border-width: 1px;
	border-style: solid;
}

<?php
	}
?>
#layout_header {
	height: <?php echo $height; ?>px;
	background: transparent;
}

a:link.mainmenu_img:link, a.mainmenu_img:visited {
	background-color: <?php echo $microtheme->topbar_color; ?> ! important;
	background: <?php echo $microtheme->topbar_color; ?> ! important;
}

<?php
	if ($hide_sitename) {
?>
/* Hide sitename */
#wrapper_header h1 {
	display: none;
}
#wrapper_header h1 a {
	display: none;
}
<?php
	}
?>

/* Hide header image */
#wrapper_header img {
	display: none;
}

/* Change the topbar color */
#elgg_topbar {

	background: <?php echo $microtheme->topbar_color; ?> repeat-x top left;
        border-bottom-color: <?php echo $microtheme->bg_color; ?>;

}
/*#page_container {
	background-color: <?php echo $microtheme->bg_color; ?>;
}*/
<?php
	}
?>
