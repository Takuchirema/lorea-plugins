<?php
/**
 * Elgg footer
 * The standard HTML footer that displays across the site
 *
 * @package ElggPowered
 *
 */

echo elgg_view_menu('footer', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));

$powered = array(
	//'tls',
	'rss',
	'openid',
	'atom',
	'pubsub',
	'foaf',
	'gpg',
	//'rdf',
	//'oauth',
	//'omb',
	//'listserv',
	//'xmpp',
	'activitystreams',
);

echo '<div class="mts clearfloat right">';
foreach($powered as $tool){
	$url = elgg_get_site_url() . "mod/powered/graphics/$tool-powered.png";
	echo elgg_view('output/url', array(
		'href' => '',
		'text' => "<img src=\"$url\" alt=\"Powered by $tool\" />",
		'class' => '',
	));
}
echo '</div>';
	

?>
      
