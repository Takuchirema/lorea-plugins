<?php

$vars['mime_type'] = $vars['entity']->getMimeType();
$vars['file_url'] = elgg_normalize_url('file/download/' . $vars['entity']->guid);

echo elgg_view('activity_streams/object/file', $vars);