<?php
$item = $vars['item'];

$subject = $item->getSubjectEntity();
$object = ActivityStreams::getObject($item);
$target = get_entity($object->container_guid);

$view = $item->getView();
if (elgg_view_exists($view)) {
	echo elgg_view($view, $vars);
} else {

$parent = ActivityStreams::getParent($object);
if ($parent) {
	$parent_id = ActivityStreams::getEntityAtomID($parent);
}

$summary = elgg_extract('summary', $vars, elgg_view('river/elements/summary', array('item' => $vars['item']), false, false, 'default'));
?>

<id><?php echo ActivityStreams::getRiverAtomID($item); ?></id>

<published><?php echo date(DATE_ATOM, $item->getPostedTime()); ?></published>

<title><?php echo htmlspecialchars(elgg_strip_tags($summary)); ?></title>

<content type="html">
       <?php echo elgg_view('output/text', array('value' => $summary)); ?>
</content>

<author>
       <?php echo elgg_view_entity($subject); ?>
</author>

<activity:verb><?php echo elgg_echo("activity_streams:verb:$item->action_type"); ?></activity:verb>

<activity:object>
       <?php echo elgg_view_entity($object); ?>
</activity:object>

<?php
	// parent must be printed in entry
	if ($parent_id) {
?>
<thr:in-reply-to ref="<?php echo $parent_id; ?>" href="<?php echo $parent_id; ?>"></thr:in-reply-to>
<?php
	}
	if ($target instanceof ElggGroup) {
?>
<activity:target>
       <?php echo elgg_view_entity($target); ?>
</activity:target>

<?php
	}
}
