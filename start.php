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

elgg_register_event_handler('init','system','favorites_init');

function favorites_page_handler($page) {
    include(elgg_get_plugins_path() . 'favorites/views/default/favorites/view.php');
    return TRUE;
}

function favorites_init(){

    elgg_register_page_handler('favorites','favorites_page_handler');

    if ( elgg_is_logged_in() ) {
        elgg_register_menu_item(
            'site',
            array(
                'name' => 'favorites', 
                'text' => elgg_echo('favorites:menu'), 
                'href' => "favorites/view/"
            )
        );
    }
    // elgg_extend_view("js/initialise_elgg", "favorites/metatags");

    $plugin_path = elgg_get_plugins_path();

    elgg_register_action(
        "favorites/add",
        $plugin_path . "favorites/actions/favorites/add.php"
    );

    elgg_register_action(
        "favorites/remove",
        $plugin_path . "favorites/actions/favorites/remove.php"
    );
    
    elgg_register_plugin_hook_handler(
        'register', 
        'menu:entity', 
        'favorites_entity_menu_setup'
    );
    elgg_register_widget_type(
        'favorites', 
        elgg_echo('favorites:widget:title'), 
        elgg_echo('favorites:widget:description')
    );
}


function favorites_entity_menu_setup($hook, $type, $return, $params) {
	if (elgg_in_context('widgets')) {
		return $return;
	}
    if ( elgg_is_logged_in() ) {

	    $entity = $params['entity'];

        if ($entity instanceof ElggEntity) {
            $options = array(
                'name' => 'favorite',
                'text' => elgg_view('favorites/button', array('entity' => $entity)),
                'is_action' => true,
                'is_trusted' => true
            );
            $return[] = ElggMenuItem::factory($options);
        }
    }
	return $return;
}

?>
