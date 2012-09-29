<?php

function webfinger_hostmeta_page_handler($page) {
	$url = elgg_get_site_url();

	$uri = get_input('uri');
	$matches = array();
	if (preg_match('/(^acct\:)(.*)(@)(.*)/' ,$uri, $matches)) {
		forward($url.'profile/'.$matches[2].'?view=xrd');
		return;
	}
	forward($uri.'?view=xrd');
}

function webfinger_init() {
	elgg_register_page_handler('webfinger', 'webfinger_hostmeta_page_handler');

	elgg_extend_view("extensions/channel", "webfinger/lrdd");
	elgg_extend_view("user/default", "webfinger/user");
}

elgg_register_event_handler('init', 'system', 'webfinger_init');
