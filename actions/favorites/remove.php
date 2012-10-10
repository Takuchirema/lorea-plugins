<?php
    $entity_guid = (int) get_input('guid');
	$user_guid = get_loggedin_userid();
    if (($entity_guid > 0) &&
        ($user_guid > 0) &&
        check_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
        if (remove_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
            // elgg_dump("Notice: Removed flag relationship between $user_guid and $entity_guid. ", FALSE, 'NOTICE');
            system_message(elgg_echo('favorites:removed'));
        } else {
            // elgg_dump("Could not add flag / fav relation between $user_guid and $entity_guid.", FALSE, 'WARN');
            register_error(elgg_echo('favorites:removefailed'));
        }
    }
    if (elgg_is_xhr()) {
        return;
    } else {
	    forward($_SERVER['HTTP_REFERER']);
    }
?>
