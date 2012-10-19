<?php
/**
 * Elgg Lorea Favorites Plugin
 */

elgg_register_event_handler('init','system','favorites_init');

function favorites_init(){

    elgg_register_page_handler('favorites','favorites_page_handler');
    elgg_extend_view("js/elgg", "js/favorites");

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

function favorites_page_handler($page) {
	echo elgg_view_page(
		elgg_echo("favorites:items"),
		elgg_view_layout(
			'one_sidebar', 
			array(
				'title' => elgg_echo("favorites:items"),
				'content' => elgg_view('favorites/view'),
				'sidebar' => elgg_view('favorites/sidebar'),
			)
		)
	);
	return true;
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
