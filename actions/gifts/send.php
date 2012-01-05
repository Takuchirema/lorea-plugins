<?php
/**
 * Elgg gifts sender action
 *
 * @package ElggGifts
 */

// Get variables
$owner_guid = get_input("owner");
$gift_type  = get_input("gift");
$note       = get_input("note", NULL);

$owner = get_entity($owner_guid);

if(!elgg_instanceof($owner, 'user') || !array_key_exists($gift_type, gifts_get_registered_gifts())) {
	register_error(elgg_echo("gifts:sendfailed"));
	forward(REFERER);
}

if($owner->guid == elgg_get_logged_in_user_guid()) {
	register_error(elgg_echo("gifts:yourself"));
	forward(REFERER);
}

$gift = new ElggObject();
$gift->subtype = "gift";
$gift->title = $note;
$gift->owner_guid = $owner_guid;
$gift->sender_guid = elgg_get_logged_in_user_guid();
$gift->gift_type = $gift_type;
$gift->access_id = ACCESS_PUBLIC;

if ($gift->save()) {
	system_message(elgg_echo("gift:sent", array($owner->name)));
	add_to_river('river/object/gift/create', 'create', $owner->guid, $gift->guid);
} else {
	register_error(elgg_echo("gifts:sendfailed"));
}

forward(REFERER);
