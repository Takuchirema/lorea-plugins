<?php
/**
* Elgg Lorea Favorites Plugin
* 
* @package
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
* @author lorea
* @copyright lorea
* @link http://lorea.org
*/

if (!isset($vars['entity'])) {
	return true;
}

if (!elgg_is_logged_in()) {
    return true;
}

$fav_entity_guid = $vars['entity']->getGUID();
$fav_user_guid = elgg_get_logged_in_user_guid();

if (!check_entity_relationship($fav_user_guid ,'flags_content', $fav_entity_guid)) {

    $fav_url = elgg_get_site_url() . "action/favorites/add/?guid={$fav_entity_guid}";
    $fav_params = array(
        'href' => $fav_url,
        'text' => elgg_view_icon('star-empty'),
        'title' => elgg_echo('favorites:add'),
        'is_action' => true,
        'is_trusted' => true,
    );
    $fav_button = elgg_view('output/url', $fav_params);

} else {

    $fav_url = elgg_get_site_url() . "action/favorites/remove/?guid={$fav_entity_guid}";
    $fav_params = array(
        'href' => $fav_url,
        'text' => elgg_view_icon('star'),
        'title' => elgg_echo('favorites:remove'),
        'is_action' => true,
        'is_trusted' => true,
    );
    $fav_button = elgg_view('output/url', $fav_params);
}

echo $fav_button;
?>
