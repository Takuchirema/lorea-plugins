<?php
/**
 * Elgg footer
 * The standard HTML footer that displays across the site
 *
 * @package ElggPowered
 *
 */

echo elgg_view_menu('footer', array('sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
echo elgg_view_menu('powered', array('sort_by' => 'priority', 'class' => 'mts clearfloat float-alt'));     
