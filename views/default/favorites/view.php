<?php

	gatekeeper();
	$fav_user_guid = elgg_get_logged_in_user_guid();
	$fav_offset = (int) get_input('offset', 0);
	$fav_limit = (int) get_input('limit', 10);

    $fav_options = array(
        'relationship_guid' => $fav_user_guid, 
        'relationship' => 'flags_content',
        'limit' => $fav_limit, 
        'full_view' => FALSE, 
        'view_type_toggle' => FALSE, 
        'pagination' => FALSE, 
        'order_by' => 'e.time_updated desc', 
        'direction' => 'ASC', 
        'pagination' => true, 
        'offset' => $fav_offset, 
        'type' => 'object');

    $fav_count = elgg_get_entities_from_relationship_count($fav_options);
	$fav_entities = elgg_get_entities_from_relationship($fav_options);

	$fav_title = elgg_echo("favorites:items");

    $fav_entity_body = elgg_view_entity_list(
        $fav_entities, 
        array('count' => $fav_count),
        $fav_offset, 
        $fav_options['limit'], 
        $fav_options['full_view'], 
        $fav_options['view_type_toggle'], 
        $fav_options['pagination']
    );

    // groups
    $fav_group_entities = elgg_get_entities_from_relationship(
        array('count' => FALSE, 
              'type' => 'group', 
              'relationship_guid' => $fav_user_guid, 
              'relationship' => 'flags_content', 
              'limit' => 0)
    );

	$fav_group_title = elgg_view_title(elgg_echo('favorites:groups'));

	foreach ($fav_group_entities as $fav_group_entity) {
        $fav_group_body .= elgg_view_entity_icon($fav_group_entity, 'small');
	}

    /*
	$fav_user_title .= elgg_view_title(elgg_echo('favorites:users'));
    $fav_users = elgg_get_entities_from_relationship(
        array('count' => FALSE, 
              'type'=>'user', 
              'relationship_guid' => $fav_user_guid, 
              'relationship' => 'flags_content', 
              'limit' => 0)
    );

	foreach ($fav_users as $fav_user) {
        $fav_user_body .= elgg_view_entity_icon($fav_group_entity, 'small');
    }
    */

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
                    $fav_entity_title .
                    $fav_entity_body,
                'sidebar' => 
                    $fav_group_title .
                    $fav_group_body 
                    // $fav_user_title .
                    // $fav_user_body
            )
        )
    );
