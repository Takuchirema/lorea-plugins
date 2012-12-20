<?php

$group = $vars['entity'];
$webpage = $group->webpage;

?>

<id><?php echo $group->getURL(); ?></id>
<title><?php echo elgg_view('output/text', array('value' => $group->name)); ?></title>
<summary><?php echo elgg_view('output/text', array('value' => $group->briefdescription)); ?></summary>
<content><?php echo elgg_view('output/longtext', array('value' => $group->description)); ?></content>
<published>
	<?php echo date(DATE_ATOM, $group->time_created); ?>
</published>
<updated>
	<?php echo date(DATE_ATOM, $group->time_updated); ?>
</updated>
<poco:note><?php echo elgg_view('output/text', array('value' => $group->description)); ?></poco:note>
<?php
if ($webpage) {
?>
<poco:urls>
  <poco:type>homepage</poco:type>
  <poco:value><?php echo $webpage; ?></poco:value>
  <poco:primary>true</poco:primary>
</poco:urls>
<?php
}
?>
<link rel="preview" type="image/png" href="<?php echo htmlspecialchars($group->getIcon('small')); ?>" />
<?php
ActivityStreams::formatAvatarIcons($group);
?>
<link rel="alternate" type="text/html" href="<?php echo htmlspecialchars($group->getURL()); ?>" />
<activity:object-type>http://activitystrea.ms/schema/1.0/group</activity:object-type>
