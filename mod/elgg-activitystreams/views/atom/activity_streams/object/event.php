<?php

$event = $vars['entity'];
$start_date = $event->start_date;
$end_date = $event->end_date;

?>

<id><?php echo ActivityStreams::getEntityAtomID($event); ?></id>
<title>
	<?php echo elgg_view('output/text', array('value' => $event->title)); ?>
</title>
<summary type="html">
	<?php echo elgg_view('output/text', array('value' => $event->description)); ?>
</summary>
<?php
if ($event->long_description) {
?>
<content type="html">
	<?php echo elgg_view('output/text', array('value' => $event->long_description)); ?>
</content>
<?php
}
?>
<published>
	<?php echo date(DATE_ATOM, $event->time_created); ?>
</published>
<updated>
	<?php echo date(DATE_ATOM, $event->time_updated); ?>
</updated>
<dtstart xmlns="urn:ietf:params:xml:ns:xcal"><?php echo ActivityStreams::formatDate($start_date); ?></dtstart>
<dtend xmlns="urn:ietf:params:xml:ns:xcal"><?php echo ActivityStreams::formatDate($end_date); ?></dtend>
<?php
if ($event->venue) {
?>
<context:location>
  <poco:address>
    <poco:formatted><?php echo htmlspecialchars($event->venue); ?></poco:formatted>
  <poco:address>
</context:location>
<?php
}
?>
<link rel="preview" type="image/png" href="<?php echo htmlspecialchars($event->getIcon('small')); ?>" />
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($event->getURL()); ?>" />
<link rel="related" type="text/html" href="<?php echo htmlspecialchars($event->address); ?>" />
<activity:object-type>http://activitystrea.ms/schema/1.0/event</activity:object-type>
<?php
echo ActivityStreams::formatTags($event);
