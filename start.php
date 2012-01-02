<?php
/**
 * Elgg gifts plugin
 * 
 * This is a rewrite of the Gifts plugin written by Christian Heckelmann
 * for Elgg 1.5.
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
	gifts_register_gift('crystal_ball', "$url_base/crystal_ball.png");
	gifts_register_gift('badge', "$url_base/badge.png");
	gifts_register_gift('organic_lettuce', "$url_base/organic_lettuce.png");
	gifts_register_gift('molotov_cocktail', "$url_base/molotov_cocktail.png");

	// Extend CSS
	elgg_extend_view('css/elgg', 'gifts/css');

	// Register a page handler, so we can have nice URLs
	elgg_register_page_handler('gifts', 'gifts_page_handler');

	// Add a new gifts widget
	elgg_register_widget_type('gifts', elgg_echo("gifts"), elgg_echo("gifts:widget:description"));

	// add a gifts link to owner blocks
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'gifts_owner_block_menu');

	// Register actions
	$action_path = elgg_get_plugins_path() . 'gifts/actions/gifts';
	elgg_register_action("gift/send", "$action_path/send.php");

}

/**
 * Dispatches gifts pages.
 * URLs take the form of
 *  User's gifts:    gifts/<username>
 *
 * @param array $page
 * @return bool
 */
function gifts_page_handler($page) {

	if (!isset($page[0])) {
		return false;
	}

	if($user = get_user_by_username($page[0])) {
		set_input('user', $user);
		// TODO page display
		return true;
	} else {
		return false;
	}
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
