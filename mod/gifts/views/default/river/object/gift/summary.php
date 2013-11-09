<?php
/**
 * Short summary of the action that occurred
 *
 * @vars['item'] ElggRiverItem
 */

$item = $vars['item'];

$owner     = $item->getSubjectEntity();
$gift      = $item->getObjectEntity();
$gift_type = elgg_echo('gifts:gift:'.$gift->gift_type);
$sender    = get_entity($gift->sender_guid);

if (!$owner->guid || !$sender->guid) {
	return false;
}

$owner_link = elgg_view('output/url', array(
	'href' => $owner->getURL(),
	'text' => $owner->name,
	'class' => 'elgg-river-subject',
	'is_trusted' => true,
));

$sender_link = elgg_view('output/url', array(
	'href' => $sender->getURL(),
	'text' => $sender->name,
	'class' => 'elgg-river-object',
	'is_trusted' => true,
));

$action = $item->action_type;
echo elgg_echo("river:$action:object:gift", array($owner_link, $gift_type, $sender_link));
