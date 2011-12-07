<?php
/**
 * Elgg suicide plugin
 *
 */

elgg_register_event_handler('init','system','suicide_init');

/**
 * Initialize suicide plugin
 */
function suicide_init() {
	// add in CSS for suicide
	elgg_extend_view('css/elgg', 'suicide/css');

	// extend view for edit profile suicide button
	elgg_extend_view('forms/profile/edit/foot', 'forms/suicide/button');
}
