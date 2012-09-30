<?php
	$entities = $vars['entities'];

	$vars["class"] = 'elgg-icon';
	foreach($entities as $entity) {
		echo "<div class='elgg-avatar'>";
		echo elgg_view_entity_icon($entity, 'tiny', $vars);
		echo "</div>";
	}
?>
