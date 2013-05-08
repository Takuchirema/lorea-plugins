<?php
/**
 * Elgg register plugin
 *
 * @package ElggRegister
 */

elgg_register_event_handler('init', 'system', 'register_init');

/**
 * Register init function
 */
function register_init() {
	elgg_register_action('register', elgg_get_plugins_path() . 'register/actions/register.php', 'public');
}
