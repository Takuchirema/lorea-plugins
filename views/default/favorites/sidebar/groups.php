<?php

elgg_push_context('widgets');

$fav_group_params = array( 
	'type' => 'group', 
	'relationship_guid' => elgg_get_logged_in_user_guid(), 
	'relationship' => 'flags_content',
	'limit' => 0,
	'pagination' => false,
	'list_type' => 'gallery',
	'gallery_class' => 'elgg-gallery-groups',
	'full_view' => false
);

$fav_group_params['count'] = true;
if(elgg_get_entities_from_relationship($fav_group_params) == 0) {
	return true;
}
$fav_group_params['count'] = false;

$fav_group_body = elgg_list_entities_from_relationship($fav_group_params);

echo elgg_view_module('aside', elgg_echo('favorites:groups'), $fav_group_body);
