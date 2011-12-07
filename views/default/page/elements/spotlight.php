<?php

echo '<div class="spotlight clearfloat">';

echo '<div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Notícies',
	'items' => array(
		'https://n-1.cc/pg/pages/view/9385/' => 'Noves eines',
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Lorea',
	'items' => array(
		'https://lorea.org/' => 'Pàgina d\'inici',
		'https://n-1.cc/pg/groups/7826/lorea/' => 'Grup de treball',
		'https://n-1.cc/pg/pages/view/14884/' => 'Donacions',
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Contacte',
	'items' => array(
		'https://lists.rhizomatik.net/listinfo/mycelia-community' => 'Llista de correu',
	),
));

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Ajuda',
	'items' => array(
		'https://n-1.cc/pg/faq/' => 'FAQ/Contacte',
		'https://n-1.cc/pg/dokuwiki/9394' => 'Com fer',
		'https://n-1.cc/pg/groups/9394/help/' => 'Grup d\'ajuda',
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Desenvolupament',
	'items' => array(
		'https://n-1.cc/pg/groups/6217/bughunting/' => 'Caçadors de bugs',
		'https://n-1.cc/pg/groups/5241/testers-de-la-red-social/' => 'Tastaolletes',
		'https://dev.lorea.org/' => 'Xarxa de desenvolupament',
		'https://github.com/lorea/'=> 'Repositori',
		
	),
));

echo '</div><div class="spotlight-column">';

echo elgg_view('page/elements/spotlight/module', array(
	'title' => 'Estadístiques',
	'items' => array(
		'members' => 'membres',
		'members/online' => 'membres actius',
		'groups/all' => 'grups',
		'pages/all' => 'pàgines',
		'blog/all' => 'blocs',
		'file/all' => 'fitxers',
		'tidypics/all' => 'fotos',
	),
));

echo '</div>';
echo '</div>';
