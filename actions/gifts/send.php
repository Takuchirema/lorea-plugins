<?php
/**
 * Elgg gifts sender action
 *
 * @package ElggGifts
 */

// Get variables
$receiver_guid = get_input("receiver");
$gift_type   = get_input("gift_type");

$receiver = new ElggUser($receiver_guid);

if(!$receiver->guid || !array_key_exists($gift_type, gifts_get_registered_gifts()) {
	register_error("gifts:sendfailed");
	forward();
}

$gift = new ElggObject();
$gift->subtype = "gift";
$gift->gift_type = $gift_type;
$gift->receiver = $receiver;
$gift->title = NULL;
$gift->access_id = ACCESS_PUBLIC;

if ($gift->save()) {
	system_message(elgg_echo("gift:sent"));
	add_to_river('river/object/gifts/create', 'create', elgg_get_logged_in_user_guid(), $gift->guid);
} else {
	register_error(elgg_echo("gift:sendfailed"));
}

forward(REFERER);
