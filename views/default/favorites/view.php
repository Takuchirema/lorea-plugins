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
        'order_by' => 'e.time_updated desc', 
    );

    $fav_title = elgg_echo("favorites:items");

    $fav_entity_body = elgg_list_entities_from_relationship($fav_options);

    // groups

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
	
	$fav_group_body = elgg_view_module('aside', elgg_echo('favorites:groups'), $fav_group_body);

	$fav_user_body = elgg_list_entities_from_relationship(array(
		'relationship' => 'flags_content',
		'relationship_guid' => elgg_get_logged_in_user_guid(),
		'type' => 'user',
		'list_type' => 'gallery',
		'gallery_class' => 'elgg-gallery-users',
		'pagination' => false,
	));

	$fav_user_body = elgg_view_module('aside', elgg_echo('favorites:users'), $fav_user_body);

    /*
	$groups .= elgg_view("favorites/extend_left");
	if (elgg_get_viewtype() != 'default') {
		$options['type'] = 'group';
        $count = elgg_get_entities_from_relationship(
            array_merge(array('count' => TRUE), $options)
        );
		$entities = elgg_get_entities_from_relationship($options);
		$group_list = elgg_view_title(elgg_echo('favorites:groups'));
        $group_list .= elgg_view_entity_list(
            $entities, 
            array(),
            $count, 
            $offset, 
            $options['limit'], 
            $options['full_view'], 
            $options['view_type_toggle'], 
            $options['pagination']
        );
	}
    */

    echo elgg_view_page(
        $fav_title, 
        elgg_view_layout(
            'one_sidebar', 
            array(
                'content' => elgg_view_title($title) .  
                    $fav_entity_body,
                'sidebar' => 
                    $fav_group_body .
                    $fav_user_body
            )
        )
    );
