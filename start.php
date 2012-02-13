<?php
/**
 * Online plugin
 * 
 * This is a rewrite of the Bogdan Nikovskiy's Online plugin,
 * written in 2009.
 *
 */

elgg_register_event_handler('init', 'system', 'online_init');

function online_init() {

	elgg_extend_view('css/elgg', 'online/css');
	elgg_extend_view('css/admin', 'online/css');
}
