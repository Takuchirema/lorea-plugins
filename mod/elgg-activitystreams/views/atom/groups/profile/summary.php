<?php
/**
 * Group profile summary
 *
 * Icon and profile fields
 *
 * @uses $vars['group']
 */

if (!isset($vars['entity']) || !$vars['entity']) {
	echo elgg_echo('groups:notfound');
	return true;
}

$group = $vars['entity'];

/**
 * Profile info for user
 */

?>

<author>

<?php echo elgg_view_entity($group); ?>

</author>

<?php echo elgg_list_river(array('container_guid' => $group->guid)); ?>
