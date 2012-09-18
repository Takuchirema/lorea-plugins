<?php

function push_init() {
	elgg_extend_view('extensions/channel', 'push/channel');

	elgg_register_event_handler('created', 'river', array('PuSH', 'updateRiverEventHandler'));

	elgg_register_simplecache_view('push/channel');
}

elgg_register_event_handler('init', 'system', 'push_init');
