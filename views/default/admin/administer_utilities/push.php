<?php
        $body = '<div id="one_column">';
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
	echo $body;
?>
