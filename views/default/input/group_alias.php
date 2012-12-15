<?php

if ('no' == elgg_get_plugin_setting('changeable_group_alias', 'group_alias')
	&& elgg_instanceof(elgg_get_page_owner_entity(), 'group')) {
	$vars['disabled'] = true;
}
echo elgg_view('input/text', $vars);

