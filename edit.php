<?php
	$object_guid = get_input('object_guid');
        $body = '<div id="one_column">';
        $body .= elgg_view("page_elements/title",array('title'=>elgg_echo('microthemes:edit')));
        $body .= '<div class="contentWrapper">';
        $body .= elgg_view('microthemes/form/edit', array('entity'=>get_entity($object_guid)))."</div></div>";
        echo page_draw('microthemes:edit' ,$body);
?>
