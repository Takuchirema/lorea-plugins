<?php
/**
 * Gift send form body
 *
 * @package ElggGifts
 */

echo '<label></label>';
echo elgg_view('input/text', array(
	'name' => 'note',
));
echo elgg_view('input/hidden', array(
	'name' => 'gift',
));
echo elgg_view('input/hidden', array(
	'name' => 'owner',
	'value' => elgg_get_page_owner_guid(),
));
echo elgg_view('input/submit', array(
	'value' => elgg_echo('send'),
	'class' => 'mts',
));
