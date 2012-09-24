<?php

$entity = $vars['entity'];

$pars = array();

$pars['name'] = $entity->name;
$pars['type'] = $entity->getType();
$pars['subtype'] = $entity->getSubtype();
$pars['atom_id'] = $entity->atom_id;
$pars['atom_link'] = $entity->atom_link;

foreach($pars as $key => $value) {
	echo "<p><b>$key:</b> $value</p>";
}
