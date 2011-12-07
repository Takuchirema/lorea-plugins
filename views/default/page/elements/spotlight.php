<?php

echo '<div class="spotlight clearfloat">';

echo '<div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Notícies',
	'items' => array(
		0 => 'Noves eines',
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Lorea',
	'items' => array(
		0 => 'Pàgina d\'inici',
		1 => 'Grup de treball',
		2 => 'Donacions',
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Contacte',
	'items' => array(
		0 => 'Llista de correu',
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Ajuda',
	'items' => array(
		0 => 'FAQ/Contacte',
		1 => 'Com fer',
		2 => 'Grup d\'ajuda',
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Desenvolupament',
	'items' => array(
		0 => 'Caçadors de bugs',
		1 => 'Tastaolletes',
		2 => 'Xarxa de desenvolupament',
		3 => 'Repositori',
		
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Estadístiques',
	'items' => array(
		0 => 'membres',
		1 => 'membres actius',
		2 => 'grups',
		3 => 'pàgines',
		4 => 'blocs',
		5 => 'fitxers',
		6 => 'fotos',
	),
));

echo '</div>';
echo '</div>';
