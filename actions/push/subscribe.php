<?php
        admin_gatekeeper();
        global $CONFIG;
        $dest_url = get_input('atom_url');

        // parse urls to see if address is really remote
        $host_parts = parse_url($CONFIG->wwwroot);
        $dest_parts = parse_url($dest_url);
        if ($host_parts['host'] !== $dest_parts['host'] && push_subscribeto($dest_url)) {
                system_message(elgg_echo('push:subscribe:success'));
        }
        else {
                register_error(elgg_echo('push:subscribe:failure').$dest_url);
        }
        forward("admin/administer_utilities/push");
?>
