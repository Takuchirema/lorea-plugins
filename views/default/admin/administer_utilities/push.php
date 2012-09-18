<?php
        $body = '<div id="one_column">';
        $body .= elgg_view("page_elements/title",array('title'=>elgg_echo('pshb:managesubscriptions')));
        if (isadminloggedin()) {
                $body .= '<div class="contentWrapper">';
                $body .= elgg_view_form('push/subscribe');
                $body .= '</div>';
        }
        $body .= '<div class="contentWrapper">';
        $body .= elgg_list_entities(array('types' => 'object',
					  'subtypes' => 'push_subscription',
					  'full_view' => false));
	$body .= "</div></div>";
 //       echo elgg_view_page('pshb:subscriptions' ,$body);
	echo $body;
?>
