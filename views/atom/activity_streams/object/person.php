<?php

$person = $vars['entity'];
$host = parse_url(elgg_get_site_url(), PHP_URL_HOST);

$name = elgg_view('output/text', array('value' => $person->name));
$username = $person->username;
$description = elgg_view('output/longtext', array('value' => $person->description));
$website = $person->website;

?>
<id><?php echo $person->getURL(); ?></id>
<uri><?php echo $person->getURL(); ?></uri>
<name><?php echo elgg_view('output/text', array('value' => $person->name)); ?></name>
<summary><?php echo elgg_view('output/text', array('value' => $person->briefdescription)); ?></summary>
<link href="<?php echo htmlspecialchars($person->getIcon('small')); ?>" rel="preview" type="image/png" media:height="40" media:width="40" />
<?php
ActivityStreams::formatAvatarIcons($person);
?>
<poco:preferredUsername><?php echo $username; ?></poco:preferredUsername>
<poco:displayName><?php echo $name; ?></poco:displayName>
<?php
if (!empty($description)) {
?>
<poco:note><?php echo $description; ?></poco:note>
<?php
}
if ($website) {
?>
<poco:urls>
  <poco:type>homepage</poco:type>
  <poco:value><?php echo $website; ?></poco:value>
  <poco:primary>true</poco:primary>
</poco:urls>
<?php
}
?>

<link href="<?php echo htmlspecialchars($person->getURL()); ?>" rel="alternate" type="text/html" />
<activity:object-type>http://activitystrea.ms/schema/1.0/person</activity:object-type>
