<?php
/**
 * Favorites JS. 
 */
?>

elgg.provide('elgg.favorites');

elgg.favorites.init = function() {
    $('.elgg-menu-item-favorite .favorites-add').live('click', elgg.favorites.add);
    $('.elgg-menu-item-favorite .favorites-remove').live('click', elgg.favorites.remove);
};

elgg.favorites.add = function(event) {
    event.preventDefault();
    var $link = $(this);
    var $actionParam = $link.attr("href").split("/").pop();
    var $postGuid = $link.attr("href").match(/guid=([^&]+)/)[1];

    elgg.action('favorites/add', {
        data: {
            guid: $postGuid
        },
        success: function(json) {
            $link.attr('href', elgg.config.wwwroot + 'action/favorites/remove/' + $actionParam);
            $link.attr('class', '.elgg-menu-item-favorite .favorites-remove');
            $link.children('span').attr('class','elgg-icon elgg-icon-star');
            $link.unbind('click');
            $link.click(elgg.favorites.remove);
        }
    });
};

elgg.favorites.remove = function(event) {
    event.preventDefault();
    var $link = $(this);
    var $actionParam = $link.attr("href").split("/").pop();
    var $postGuid = $link.attr("href").match(/guid=([^&]+)/)[1];

    elgg.action('favorites/remove', {
        data: {
            guid: $postGuid
        },
        success: function(json) {
            $link.attr("href", elgg.config.wwwroot + 'action/favorites/add/' + $actionParam);
            $link.attr('class', '.elgg-menu-item-favorite .favorites-add');
            $link.children('span').attr('class','elgg-icon elgg-icon-star-empty');
            $link.unbind('click');
            $link.click(elgg.favorites.add);
        }
    });
};

elgg.register_hook_handler('init', 'system', elgg.favorites.init);

