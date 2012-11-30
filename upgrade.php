<?php
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

admin_gatekeeper();
set_time_limit(0);

echo "upgrade group alias";
$previous_access = elgg_set_ignore_access(true);
group_alias_run_upgrades();
elgg_set_ignore_access($previous_access);

echo "DONE";
