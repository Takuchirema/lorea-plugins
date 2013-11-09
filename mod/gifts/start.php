<?php
/**
 * Elgg gifts plugin
 * 
 * This is a rewrite of the Gifts plugin written by Christian Heckelmann
 * for Elgg 1.5.
 * 
 * TODO:
 *    * A page to view your gifts
 *    * Abailability to upload own gifts
 *    * Abailability to set the access for gifts
 *
 * @package ElggGifts
 */

elgg_register_event_handler('init', 'system', 'gifts_init');

/**
 * Gifts plugin initialization functions.
 */
function gifts_init() {
	
	$url_base = elgg_get_site_url() . 'mod/gifts/graphics';
	gifts_register_gift('banana', "$url_base/banana.png");
	gifts_register_gift('flower', "$url_base/flower.png");
	gifts_register_gift('coal_lorry', "$url_base/coal_lorry.png");
	gifts_register_gift('badge', "$url_base/badge.png");
	gifts_register_gift('seed', "$url_base/seed.png");
	gifts_register_gift('heart', "$url_base/heart.png");
	gifts_register_gift('organic_carrot', "$url_base/organic_carrot.png");
	gifts_register_gift('molotov_cocktail', "$url_base/molotov_cocktail.png");

	// Extend CSS
	elgg_extend_view('css/elgg', 'gifts/css');
	
	// register the gifts's JavaScript
	$gifts_js = elgg_get_simplecache_url('js', 'gifts/send');
	elgg_register_simplecache_view('js/gifts/send');
	elgg_register_js('elgg.gifts', $gifts_js);

	// Register a page handler, so we can have nice URLs
	//elgg_register_page_handler('gifts', 'gifts_page_handler');

	// Add a new gifts widget
	elgg_register_widget_type('gifts', elgg_echo("gifts"), elgg_echo("gifts:widget:description"));

	// Register actions
	$action_path = elgg_get_plugins_path() . 'gifts/actions/gifts';
	elgg_register_action("gifts/send", "$action_path/send.php");

}

/**
 * Dispatches gifts pages.
 * URLs take the form of
 *  User's gifts:    gifts/owner/<username>
 *
 * @param array $page
 * @return bool
 */
function gifts_page_handler($page) {

	if (!isset($page[0])) {
		return false;
	}
	
	$user = get_user_by_username($page[1]);
	
	if(!$user) {
		return false;
	}
	
	$params = array(
		'title' => elgg_echo('gifts:view'),
		'content' => elgg_view('gifts/view', array('owner' => $user)),
		'filter' => '',
	);
	
	$body = elgg_view_layout('content', $params);
	echo elgg_view_page($params['title'], $body);
	return true;
}

function gifts_register_gift($name, $img) {
	global $CONFIG;
	
	if(isset($CONFIG->gifts) && is_array($CONFIG->gifts)) {
		$CONFIG->gifts[$name] = $img;
	} else {
		$CONFIG->gifts = array($name => $img);
	}
}

function gifts_unregister_gift($name) {
	global $CONFIG;
	unset($CONFIG->gifts[$name]);
}

function gifts_get_registered_gifts() {
	return elgg_get_config('gifts');
}
