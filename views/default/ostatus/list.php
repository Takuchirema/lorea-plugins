<?php
	$entities = $vars['entities'];

	$vars["class"] = 'elgg-icon';
	echo "<ul class='elgg-list list-follow'>";
	foreach($entities as $entity) {
		echo "<li class='elgg-item'>";
		echo "<div class='elgg-avatar'>";
		echo elgg_view_entity_icon($entity, 'tiny', $vars);
		echo "</div>";
		echo "</li>";
	}
	echo "</ul>";
?>
