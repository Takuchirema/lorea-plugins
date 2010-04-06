<?php
	global $CONFIG;

	$isself = get_input('assign_to') == get_loggedin_userid();

        $body = '<div id="one_column">';
        $body .= elgg_view("page_elements/title",array('title'=>elgg_echo('microthemes:view')));
        $body .= '<div class="contentWrapper">';
	//$body .= "<a href='".$CONFIG->wwwroot."pg/microthemes/edit'>".elgg_echo('microthemes:create')."</a>";
        $form_body = elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('microthemes:create')));
        $body .= "<div style='display:inline-block;'>".elgg_view('input/form', array('action' => $CONFIG->wwwroot."pg/microthemes/edit", 'body' => $form_body))."</div> ";
	$body .= elgg_echo('microthemes:themeinstructions');
        $body .= '</div><div class="contentWrapper">';
        $body .= elgg_list_entities(array('types'=>'object', 'subtypes'=>'microtheme', 'full_view'=>false, 'assign_to'=>666, 'pagination' => FALSE, 'limit' => 10))."</div></div>";
        echo page_draw('microthemes:manage' ,$body);
?>
