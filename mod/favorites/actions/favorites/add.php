<?php
    $entity_guid = (int) get_input('guid');
	$user_guid = elgg_get_logged_in_user_guid();
    if (($entity_guid > 0) && 
        ($user_guid > 0) &&
        !check_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
        if (add_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
            // elgg_dump("Notice: Added flag relationship between $user_guid and $entity_guid. ", FALSE, 'NOTICE');
            system_message(elgg_echo('favorites:added'));
        } else {
            // elgg_dump("Could not add flag / fav relation between $user_guid and $entity_guid.", FALSE, 'WARN');
            register_error(elgg_echo('favorites:addfailed'));
        }
    }
    if (!elgg_is_xhr()) {
	    forward($_SERVER['HTTP_REFERER']);
    }
