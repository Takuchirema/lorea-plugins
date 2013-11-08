<?php

$user = $vars['entity'];
$is_online = $user->last_action >= time() - 600;

if ($is_online && $user->isFriend()) {
	echo elgg_view_icon('online', 'icon-over hidden');
}
