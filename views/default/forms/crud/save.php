<?php
/**
 * Edit crud form
 *
 * @package CRUD
 */

$crud = $vars['crud'];
$crud_type = $crud->crud_type;
$object = get_entity($vars['guid']);
$vars['entity'] = $object;

$variables = elgg_get_config($crud_type);

foreach ($variables as $name => $type) {
?>
<div>
	<label><?php echo elgg_echo("$crud->module:$crud->crud_type:$name") ?></label>
	<?php
		if ($type != 'longtext') {
			echo '<br />';
		}
	?>
	<?php echo elgg_view("input/$type", array(
			'name' => $name,
			'value' => $vars[$name],
		));
	?>
</div>
<?php
}

$action_buttons = '';
$delete_link = '';

if ($vars['guid']) {
	// add a delete button if editing
	$delete_url = "action/crud/delete?guid={$vars['guid']}";
	$delete_link = elgg_view('output/confirmlink', array(
		'href' => $delete_url,
		'text' => elgg_echo('delete'),
		'class' => 'elgg-button elgg-button-delete elgg-state-disabled float-alt'
	));
}

$save_button = elgg_view('input/submit', array(
	'value' => elgg_echo('save'),
	'name' => 'save',
));
$action_buttons = $save_button . $delete_link;

$save_status = elgg_echo('crud:save_status');
if ($vars['guid']) {
	$entity = get_entity($vars['guid']);
	$saved = date('F j, Y @ H:i', $entity->time_created);
} else {
	$saved = elgg_echo('crud:never');
}

// hidden inputs
$container_guid_input = elgg_view('input/hidden', array('name' => 'container_guid', 'value' => elgg_get_page_owner_guid()));
$guid_input = elgg_view('input/hidden', array('name' => 'guid', 'value' => $vars['guid']));
$crud_input = elgg_view('input/hidden', array('name' => 'crud', 'value' => $crud_type));


echo <<<___HTML

<div class="elgg-foot">
	<div class="elgg-subtext mbm">
	$save_status <span class="object-save-status-time">$saved</span>
	</div>

	$guid_input
	$container_guid_input
	$crud_input

	$action_buttons
</div>

___HTML;
