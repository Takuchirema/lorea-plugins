<?php
$item = $vars['item'];

$object = ActivityStreams::getObject($item);

$target = get_entity($object->container_guid);
if ($target) {
?>
<link rel="ostatus:attention" href="<?php echo $target->getURL(); ?>"/>
<?php
}
