<?php

$type = $vars['type'];
if (empty($type))
	$type = 'http://activitystrea.ms/schema/1.0/article';

$article = $vars['entity'];

?>

<id><?php echo ActivityStreams::getEntityAtomID($article); ?></id>
<title>
	<?php echo elgg_view('output/text', array('value' => $article->title)); ?>
</title>
<?php
if ($article->excerpt) {
?>
<summary>
	<?php echo elgg_view('output/text', array('value' => $article->excerpt)); ?>
</summary>
<?php
}
?>
<content type="html">
	<?php echo elgg_view('output/text', array('value' => $article->description)); ?>
</content>
<published>
	<?php echo date(DATE_ATOM, $article->time_created); ?>
</published>
<updated>
	<?php echo date(DATE_ATOM, $article->time_updated); ?>
</updated>
<activity:object-type><?php echo $type; ?></activity:object-type>
<link rel="preview" type="image/png" href="<?php echo htmlspecialchars($article->getIcon('small')); ?>" />
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($article->getURL()); ?>" />
<?php
echo ActivityStreams::formatTags($article);
