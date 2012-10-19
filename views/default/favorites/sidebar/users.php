<?php

$fav_user_body = elgg_list_entities_from_relationship(array(
	'relationship' => 'flags_content',
	'relationship_guid' => elgg_get_logged_in_user_guid(),
	'type' => 'user',
	'list_type' => 'gallery',
	'gallery_class' => 'elgg-gallery-users',
	'pagination' => false,
));

echo elgg_view_module('aside', elgg_echo('favorites:users'), $fav_user_body);
