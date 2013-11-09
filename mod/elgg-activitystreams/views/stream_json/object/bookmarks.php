<?php

$vars['target_url'] = $vars['entity']->address;
echo elgg_view('activity_streams/object/bookmark', $vars);