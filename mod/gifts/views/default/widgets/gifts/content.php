<?php
/**
 * Elgg gifts widget
 *
 * @package ElggGifts
 */

$gifts = gifts_get_registered_gifts();
$owner = elgg_get_page_owner_entity();
$user  = elgg_get_logged_in_user_entity();

elgg_load_js('elgg.gifts');

echo '<ul class="elgg-gallery">';
foreach ($gifts as $gift => $gift_img) {
	$gift_name = elgg_echo("gifts:gift:$gift");
	$give = elgg_echo("gifts:give", array(strtolower($gift_name), $owner->name));
	$url = 'action/gifts/send/?'.http_build_query(array('owner' => $owner->guid, 'gift' => $gift));
	$img = '<img src="'.$gift_img.'" alt="'.$gift_name.'" />';
	
	echo '<li class="elgg-item">';
	if($owner->isFriendsWith($user->guid)) {
		echo elgg_view('output/url', array(
			'href' => $url,
			'text' => $img,
			'title' => $give,
			'id' => "gift-$gift",
			'class' => 'gift',
			'is_trusted' => true,
			'is_action' => true,
		));
	} else {
		echo $img;
	}
	
	$num = elgg_get_entities_from_metadata(array(
		'type' => 'object',
		'subtype' => 'gift',
		'owner_guid' => $owner->guid,
		'metadata_name' => 'gift_type',
		'metadata_value' => $gift,
		'count' => true,
	));
	
	echo '<div class="gift-count">'.($num > 0 ? $num : '-').'</div>';
	
	echo '</li>';
}
echo '</ul>';
echo elgg_view_form('gifts/send', array(
	'id' => 'gifts_note',
	'class' => 'mtm hidden',
), array());
