<?php

if (!elgg_get_config('changeable_group_alias') && elgg_instanceof(elgg_get_page_owner_entity(), 'group')) {
	$vars['disabled'] = true;
}
echo elgg_view('input/text', $vars);

