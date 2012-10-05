<?php
	$group = $vars['entity'];
	$options = array('type' => 'object',
			'subtype' => 'moderated_discussion',
			'count' => true,
			'container_guid' => $group->guid);
	$count = elgg_get_entities($options);

	if ($count) {
		echo "<div>".elgg_echo('elggman:moderation:messages', array($count))."</div>";
		foreach(array('moderate') as $action) {
			
			echo elgg_view('output/url', array(
						'href'=> "elggman/$action/".$group->guid,
						'text' => elgg_echo("elggman:$action"),
						'class' => 'elgg-button',
						));
		}
	}
	else {
		echo elgg_echo('elggman:moderation:nocontent');
	}
	foreach(array('whitelist', 'blacklist') as $action) {
		
		echo elgg_view('output/url', array(
					'href'=> "elggman/$action/".$group->guid,
					'text' => elgg_echo("elggman:$action"),
					'class' => 'elgg-button',
					));
	}
?>
