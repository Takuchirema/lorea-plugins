<?php
/**
 * Group Alias plugin settings
 */

// set default value
if (!isset($vars['entity']->changeable_group_alias)) {
	$vars['entity']->changeable_group_alias = 'no';
}

echo '<div>';
echo elgg_echo('groups:alias:changeable');
echo ' ';
echo elgg_view('input/select', array(
	'name' => 'params[changeable_group_alias]',
	'options_values' => array(
		'no' => elgg_echo('option:no'),
		'yes' => elgg_echo('option:yes')
	),
	'value' => $vars['entity']->changeable_group_alias,
));
echo '<p class="tip">';
echo elgg_echo('groups:alias:changeable:may_break_urls');
echo '</p>';
echo '</div>';
