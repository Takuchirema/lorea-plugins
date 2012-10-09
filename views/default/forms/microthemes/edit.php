<?php
/**
 * Elgg microtheme upload/save form
 *
 * @package ElggMicrothemes
 */

// once elgg_view stops throwing all sorts of junk into $vars, we can use 
$guid = elgg_extract('guid', $vars, null);
$assign_to = elgg_extract('assign_to', $vars, null);
$variables = elgg_get_config('microtheme');
if (!$variables) {
	$variables = array();
}
$entity = get_entity($guid);

$values = array();
$opt_values = array();

if ($entity) {
	// auto properties
	foreach ($variables as $name => $field) {
		$input[$name] = $entity->$name;
	}

	// hardcoded properties
	$title = $entity->title;
	$tags = implode(', ', $entity->tags);
	$access_id = $entity->access_id;
	$bg_color = $entity->bg_color;
	$topbar_color = $entity->topbar_color;

	// options
	if ($entity->repeatx === 1)
		array_push($values, 'repeatx');
	if ($entity->repeaty === 1)
		array_push($values, 'repeaty');
	if ($entity->bg_alignment)
		$align = $entity->bg_alignment;
	else
		$align = 'left';
}
else {
	$input['height'] = '50';
	$input['margin'] = '50';
	$input['bg_y'] = '110';
	array_push($values, 'repeatx');
	array_push($values, 'repeaty');
	$align = 'left';
	$bg_color = '#EEE';
	$topbar_color = '#333';
}

if ($guid) {
	$label = elgg_echo("microthemes:background:replace");
} else {
	$label = elgg_echo("microthemes:background:upload");
}

?>

<div>
	<label><?php echo elgg_echo('title'); ?></label><br />
	<?php echo elgg_view('input/text', array('name' => 'title', 'value' => $title)); ?>
</div>
<div>
	<label><?php echo elgg_echo("microthemes:topbar_color"); ?></label><br />
	<?php echo elgg_view('input/color', array('name' => 'topbar_color', 'value' => $topbar_color)); ?>
</div>
<div>
	<label><?php echo elgg_echo("microthemes:bg_color"); ?></label><br />
	<?php echo elgg_view('input/color', array('name' => 'background_color', 'value' => $bg_color)); ?>
</div>
<div>
	<label><?php echo $label; ?></label><br />
	<?php echo elgg_view('input/file', array('name' => 'background_image')); ?>
</div>
<?php
	$form_body = '<p><label>'.elgg_echo('microthemes:repeatx').' </label><br />';
        $form_body .= elgg_view("input/checkboxes", array(
                        'options' => array(elgg_echo('microthemes:repeatx')=>'repeatx',
                        elgg_echo('microthemes:repeaty')=>'repeaty'),
                        'internalname'=>'repeat',
                        'value' => $values));
        $form_body .= '</p>';
        $form_body .= '<p><label>'.elgg_echo('microthemes:bg_alignment').' </label><br />';
        $form_body .= elgg_view("input/radio", array('value'=>$align,
                                'internalname' => 'alignment',
                                'options'=>array(
                                elgg_echo('microthemes:background:alignment:left')=>'left',
                                elgg_echo('microthemes:background:alignment:center')=>'center',
                                elgg_echo('microthemes:background:alignment:right')=>'right')));
        $form_body .= '</p>';

	foreach ($variables as $name => $field) {
		$form_body .= '<p><label>'.elgg_echo("microthemes:$name").' </label><br />';
		$form_body .= elgg_view('input/text', array('name' => $name, 'value' => $input[$name]));
	}
	
	echo $form_body;
?>
<div>
	<label><?php echo elgg_echo('tags'); ?></label>
	<?php echo elgg_view('input/tags', array('name' => 'tags', 'value' => $tags)); ?>
</div>
<div class="elgg-foot">
<?php
if ($guid) {
	echo elgg_view('input/hidden', array('name' => 'guid', 'value' => $guid));
}
echo elgg_view('input/hidden', array('name' => 'assign_to', 'value' => $assign_to));

echo elgg_view('input/submit', array('value' => elgg_echo("save")));

?>
</div>
