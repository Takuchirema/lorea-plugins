<?php
/**
 * Elgg gifts widget
 *
 * @package ElggGifts
 */

$gifts = gifts_get_registered_gifts();
$owner = elgg_get_page_owner_entity();

elgg_load_js('elgg.gifts');

echo '<ul class="elgg-gallery">';
foreach ($gifts as $gift => $gift_img) {
	$gift_name = elgg_echo("gifts:gift:$gift");
	echo '<li class="elgg-item">';
	echo elgg_view('output/url', array(
		'title' => elgg_echo("gifts:give", array(strtolower($gift_name), $owner->name)),
		'href' => false,
		'text' => '<img id="gift-'.$gift.'" class="gift" src="'.$gift_img.'" alt="'.$gift_name.'" />',
	));
	echo '</li>';
}
echo '</ul>';
echo elgg_view_form('gifts/send', array(
	'id' => 'gifts_note',
	'class' => 'mtm hidden',
), array());
