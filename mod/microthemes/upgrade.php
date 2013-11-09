<?php
require_once(dirname(dirname(dirname(__FILE__))) . "/engine/start.php");

admin_gatekeeper();
set_time_limit(0);

echo "upgrade microthemes";
$previous_access = elgg_set_ignore_access(true);
microthemes_run_upgrades();
elgg_set_ignore_access($previous_access);

echo "DONE";
