<?php
/**
 * Elgg friendly time plugin
 *
 * @package ElggFriendlyTime
 */

elgg_register_event_handler('init', 'system', 'friendly_time_init');

/**
 * Friendly time plugin initialization functions.
 */
function friendly_time_init() {

	// Extend JS
	elgg_extend_view('js/elgg', 'friendly_time/js');

}
