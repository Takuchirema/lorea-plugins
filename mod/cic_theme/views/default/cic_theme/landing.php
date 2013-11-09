<?php

elgg_load_css('cic:landing_page');

$title = elgg_echo('cic_theme:landing:title');
$subtitle = elgg_echo('cic_theme:landing:subtitle');

echo <<<HTML
<div id="cic-landing">
<div id="cic-landing-inner">
<p id="cic-landing-title">$title</p>
<p id="cic-landing-subtitle">$subtitle</p>
</div>
</div>
HTML;
