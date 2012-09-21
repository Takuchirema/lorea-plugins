<?php

function salmon_page_handler($page) {
	if (count($page) && $page[0] === "key") {
		$username = $page[1];
		$user = get_user_by_username($username);
		if ($username && $user) {
			$key = new SalmonKey($user);
			header("Content-type: application/magic-public-key");
			echo $key->echoKey();
			return true;
		}
	}
	SalmonProtocol::request($page);
}


function salmon_init() {
	// xrds links on user page
	elgg_extend_view("user/default", "salmon/user");

	// xrds links on server page
	elgg_extend_view("xrds/services", "salmon/service");

	// atom links in feed
	elgg_extend_view("extensions/channel", "salmon/endpoint");

	// page handler
	elgg_register_page_handler('salmon','salmon_page_handler');

	// event hooks to generate salmon messages
	elgg_register_event_handler('created', 'river', array('SalmonGenerator', 'onRiverUpdate'));
}

elgg_register_event_handler('init', 'system', 'salmon_init');
