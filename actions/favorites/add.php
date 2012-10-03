<?php
    $entity_guid = (int) get_input('guid');
	$user_guid = get_loggedin_userid();
	if (!check_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
        if (add_entity_relationship($user_guid ,'flags_content', $entity_guid)) {
            // elgg_dump("Notice: Added flag relationship between $user_guid and $entity_guid. ", FALSE, 'NOTICE');
            system_message(elgg_echo('favorites:added'));
        } else {
            // elgg_dump("Could not add flag / fav relation between $user_guid and $entity_guid.", FALSE, 'WARN');
            register_error(elgg_echo('favorites:addfailed'));
        }
    }
	forward($_SERVER['HTTP_REFERER']);
?>
