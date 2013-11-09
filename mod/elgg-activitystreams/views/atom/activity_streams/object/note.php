<?php

$note = $vars['entity'];
$title = elgg_view('output/text', array('value' => $note->title));
?>

<id><?php echo ActivityStreams::getEntityAtomID($note); ?></id>
<?php
if ($title) {
?>
<title><?php echo $title; ?></title>
<?php
}
?>
<content><![CDATA[<?php
	echo elgg_view('output/longtext', array('value' => $note->description));
?>]]></content>
<published>
	<?php echo date(DATE_ATOM, $note->time_created); ?>
</published>
<updated>
	<?php echo date(DATE_ATOM, $note->time_updated); ?>
</updated>
<link rel="preview" type="image/png" href="<?php echo htmlspecialchars($note->getIcon('small')); ?>" />
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($note->getURL()); ?>" />
<activity:object-type>http://activitystrea.ms/schema/1.0/note</activity:object-type>

