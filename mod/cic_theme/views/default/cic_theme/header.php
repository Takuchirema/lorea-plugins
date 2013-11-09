<?php

$context = elgg_get_context();

$title = $vars['title'];

if ($context === 'main' && $title == elgg_echo('content:latest')) {

	echo elgg_view('cic_theme/landing');

}
