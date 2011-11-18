<?php
/**
 * Elgg powered
 *
 * @package ElggPowered
 */

elgg_register_event_handler('init', 'system', 'powered_init');

/**
 * Initialise the powered tool
 */
function powered_init(){
	
	// Register some powered tools to display in footer menu
	elgg_register_plugin_hook_handler('register', 'menu:powered', 'powered_menu');
}

function powered_menu($hook, $type, $return, $params){
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
	
	foreach($powered as $tool){
		$img_url = elgg_get_site_url() . "mod/powered/graphics/$tool-powered.png";
		$text = "<img src=\"$img_url\" alt=\"Powered by $tool\" title=\"Powered by $tool\" />";
		$return[] = new ElggMenuItem($tool, $text, false);
	}
	return $return;
}
