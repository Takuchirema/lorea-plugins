<?php
/**
 * Icon to AS/media_link
 */

$ml_vars = array(
	'media_url' => $vars['entity']->getIconURL()
);

echo elgg_view('activity_streams/media_link', $ml_vars);