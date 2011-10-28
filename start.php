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
	elgg_extend_view('css/elgg','powered/css');
	// Extend footer
	elgg_extend_view("footer/analytics", "powered/footer");
}


?>
