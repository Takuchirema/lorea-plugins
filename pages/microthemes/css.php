<?php
header("Content-type: text/css");

// cache this file
header('Expires: ' . date('r',time() + 864000));
header("Pragma: public", true);
header("Cache-Control: public", true);

$microtheme = elgg_get_page_owner_entity();

$prefix = "microthemes/banner_{$microtheme->guid}large";
// check if file exists

$image_url = elgg_get_site_url() . 'mod/microthemes/thumbnail.php?guid=' . $microtheme->guid."&size=master&last_update=$microtheme->time_updated";

error_log("generate css");

$bgopts = '';
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

$height = 50;
if ($microtheme->height) {
	$height = $microtheme->height;
}


$margin = 50;
if ($microtheme->margin) {
	$margin = $microtheme->margin;
}


$color = "#EEE";
$topbar_color = "#333";

if ($microtheme->bg_color) {
	$color = $microtheme->bg_color;
}

if ($microtheme->topbar_color) {
	$topbar_color = $microtheme->topbar_color;
}


?>

body {
	background-color: <?php echo $color; ?>;
	background: <?php echo $color; ?> url(<?php echo $image_url; ?>) <?php echo $bgopts; ?>;
}

.elgg-page-body {
	margin-top: <?php echo $height; ?>px;
	margin-left: <?php echo $margin; ?>px;
	margin-right: <?php echo $margin; ?>px;
}

.elgg-inner > .elgg-layout{
	background-color: #EEE;
	opacity: 1.0;
}

.elgg-page-body > .elgg-inner {
	border-radius:0 0 1px 1px;
	border: 6px solid #AAA;
	
}

<?php
if ($topbar_color) {
?>
.elgg-page-header, .elgg-page-topbar, .elgg-menu-site > li a {
	background-color: <?php echo $topbar_color; ?>;
}
<?php
}
?>

.elgg-page-footer > .elgg-inner {
	border-top: 0px none !important;
}

.elgg-page-footer {
	background: none !important;
	background-color: auto;
}

.spotlight {
	border: 6px solid #AAA;
	background-color: #EEE;
	border-radius:1px;
	padding: 20px;
}
