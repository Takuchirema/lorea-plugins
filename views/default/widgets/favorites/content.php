<?php

$fav_pages_num  = (int) $vars['entity']->pages_num;
if (!$fav_pages_num) {
    $fav_pages_num = 4;
}

$fav_user_guid = elgg_get_page_owner_guid();
$fav_offset = 0;

$fav_options = array(
    'relationship_guid' => $fav_user_guid,
    'relationship'=>'flags_content',
    'limit' => $fav_pages_num, 
    'full_view' => FALSE, 
    'view_type_toggle' => FALSE, 
    'pagination' => FALSE, 
    'order_by'=>'e.time_updated desc', 
    'direction'=>'ASC', 
    'offset'=>$fav_offset, 
    'type'=>'object'
);

$fav_entities = elgg_get_entities_from_relationship($fav_options);
$fav_count = elgg_get_entities_from_relationship_count($fav_options);
$fav_title = elgg_view_title(elgg_echo("favorites:items"));

echo elgg_view_entity_list(
    $fav_entities, 
    $fav_count, 
    $fav_offset, 
    $fav_options['limit'], 
    $fav_options['full_view'], 
    $fav_options['view_type_toggle'], 
    $fav_options['pagination']
);
     
?>
