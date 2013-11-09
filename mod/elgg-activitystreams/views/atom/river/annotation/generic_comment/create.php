<?php
$item = $vars['item'];

$subject = $item->getSubjectEntity();
$object = $item->getAnnotation();
$target = get_entity($object->container_guid);

$summary = elgg_extract('summary', $vars, elgg_view('river/elements/summary', array('item' => $vars['item']), false, false, 'default'));

$parent = get_entity($object->entity_guid);
if ($parent) {
	$parent_id = ActivityStreams::getEntityAtomID($parent);
}
?>

<id><?php echo ActivityStreams::getRiverAtomID($item); ?></id>

<published><?php echo date(DATE_ATOM, $item->getPostedTime()); ?></published>

<title><?php echo elgg_strip_tags($summary); ?></title>

<content type="html">
       <?php echo elgg_view('output/text', array('value' => $summary)); ?>
</content>

<author>
       <?php echo elgg_view_entity($subject); ?>
</author>

<activity:verb><?php echo elgg_echo("activity_streams:verb:$item->action_type"); ?></activity:verb>

<?php
if ($object) {
?>
<activity:object>
       <?php echo elgg_view_annotation($object); ?>
</activity:object>

<?php
}

if ($target instanceof ElggGroup) {
?>
<activity:target>
       <?php echo elgg_view_entity($target); ?>
</activity:target>

<?php
}
?>
<?php
if ($parent) {
?>
<thr:in-reply-to ref="<?php echo $parent_id; ?>" href="<?php echo $parent_id; ?>"></thr:in-reply-to>
<?php
}
?>
