<?php
$item = $vars['item'];

$provenance = AtomRiverMapper::getRiverProvenance($item->id);

if (!empty($provenance)) {
	echo $provenance;
}
