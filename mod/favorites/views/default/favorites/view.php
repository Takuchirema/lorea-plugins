<?php

if (!elgg_is_logged_in()) {
	return;
}

$fav_options = array(
	'type' => 'object',
	'relationship_guid' => elgg_get_logged_in_user_guid(), 
	'relationship' => 'flags_content', 
	'full_view' => FALSE, 
	'view_type_toggle' => FALSE, 
);

echo elgg_list_entities_from_relationship($fav_options);
