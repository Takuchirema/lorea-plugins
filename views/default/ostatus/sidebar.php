<?php

if (in_array(elgg_get_context(), array('ostatus', 'activity')) && elgg_get_logged_in_user_entity()) {
   $user = elgg_get_logged_in_user_entity();
   $options = array('type'=>'user',
		 'relationship' => 'follow',
		 'list_class' => 'list-follow',
		 'relationship_guid' => $user->guid,
		'full_view' => FALSE,
		 'inverse_relationship' => FALSE);

   // icons, for now use internal
   //$icon = elgg_view_icon('add');
 
   $site_url = elgg_get_site_url();
   $icon_url = $site_url . 'mod/elgg-ostatus/graphics/icon-subscribe.png';
   $icon = "<img src='$icon_url' />";

   // List following
   $context = elgg_get_context();
   elgg_set_context('gallery');
   $entities = elgg_list_entities_from_relationship($options);
   if ($entities) {
     echo "<h3>".elgg_echo('ostatus:following')."</h3>";
     echo $entities;
   }
   echo elgg_view("output/url", array('href' => 'ostatus/subscribe',
				      'class' => 'ostatus',
					'text' => $icon . " " . elgg_echo('ostatus:subscribe')));
   echo "<div class='clearfloat'></div>";

   $options['inverse_relationship'] = TRUE;

   // List followed by
   $entities = elgg_list_entities_from_relationship($options);
   if ($entities) {
      echo "<h3>".elgg_echo('ostatus:followed_by')."</h3>";
      echo $entities;
   }

   echo "<div class='clearfloat'></div>";

   elgg_set_context($context);
   $options['inverse_relationship'] = FALSE;
   $options['type'] = 'group';
   $options['relationship'] = 'member';

   // List followed by
   $entities = elgg_get_entities_from_relationship($options);
   if ($entities) {
      echo "<h3>".elgg_echo('ostatus:groups')."</h3>";
      echo elgg_view('ostatus/list', array('entities' => $entities));
   }


}

?>
