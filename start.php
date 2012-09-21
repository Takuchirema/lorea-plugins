<?php

function xrds_hostmeta_page_handler($page) {
	elgg_set_viewtype('xrds');
	$body = elgg_view('host-meta');
	echo elgg_view_page('', $body);
}

function xrds_init() {
	elgg_register_page_handler('.well-known', 'xrds_hostmeta_page_handler');

	elgg_extend_view("page/elements/head", "xrds/header");
}

elgg_register_event_handler('init', 'system', 'xrds_init');
