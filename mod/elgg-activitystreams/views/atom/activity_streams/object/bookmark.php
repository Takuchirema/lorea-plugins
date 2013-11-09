<?php

$bookmark = $vars['entity'];

?>

<id><?php echo ActivityStreams::getEntityAtomID($bookmark); ?></id>
<title>
	<?php echo elgg_view('output/text', array('value' => $bookmark->title)); ?>
</title>
<summary type="html">
	<?php echo elgg_view('output/text', array('value' => $bookmark->description)); ?>
</summary>
<published>
	<?php echo date(DATE_ATOM, $bookmark->time_created); ?>
</published>
<updated>
	<?php echo date(DATE_ATOM, $bookmark->time_updated); ?>
</updated>
<link rel="preview" type="image/png" href="<?php echo htmlspecialchars($bookmark->getIcon('small')); ?>" />
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($bookmark->getURL()); ?>" />
<link rel="related" type="text/html" href="<?php echo htmlspecialchars($bookmark->address); ?>" />
<activity:object-type>http://activitystrea.ms/schema/1.0/bookmark</activity:object-type>
<?php
echo ActivityStreams::formatTags($bookmark);
