<?php
/**
 * Elgg microtheme thumbnail
 *
 * @package ElggMicrotheme
 */

// Get engine
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");


// Get file GUID
$guid = (int) get_input('guid', 0);


// Get file thumbnail size
$size = get_input('size', 'small');

$microtheme = get_entity($guid);
if (!$microtheme || $microtheme->getSubtype() != "microtheme") {
	exit;
}


// Get file thumbnail
switch ($size) {
	case "small":
		$thumbfile = 'small';
		break;
	case "medium":
		$thumbfile = 'medium';
		break;
	case "master":
		$thumbfile = '_master.jpg';
		break;
	case "large":
	default:
		$thumbfile = 'large';
		break;
}

$prefix = "microthemes/banner_{$guid}$thumbfile";

// Grab the file
if ($thumbfile && !empty($thumbfile)) {
	$readfile = new ElggFile();
	$readfile->owner_guid = $microtheme->owner_guid;
	$readfile->setFilename($prefix);
	$mime = $readfile->getMimeType();
	$contents = $readfile->grabFile();

	// caching images for 10 days
	header("Content-type: $mime");
	header('Expires: ' . date('r',time() + 864000));
	header("Pragma: public", true);
	header("Cache-Control: public", true);
	header("Content-Length: " . strlen($contents));

	echo $contents;
	exit;
}
