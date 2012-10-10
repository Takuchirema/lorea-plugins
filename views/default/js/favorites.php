<?php
$site_url = elgg_get_site_url();
?>

elgg.provide('elgg.favorites');

elgg.favorites.init = function() {
    $('.elgg-menu-item-favorite .favorites-add').live('click', elgg.favorites.add);
    $('.elgg-menu-item-favorite .favorites-remove').live('click', elgg.favorites.remove);
    console.log('favorites loaded');
};

elgg.favorites.add = function(event) {
	event.preventDefault();
    console.log('favorites add');

	var $link = $(this);
    var $actionParam = $link.attr("href").split("/").pop();
	var $postGuid = $link.attr("href").match(/guid=([^&]+)/)[1];
    console.log($postGuid);

    elgg.action('favorites/add', {
        data: {
            guid: $postGuid
        },
        success: function(json) {
            console.log('yes added');
            $link.attr('href', '<?php echo $site_url; ?>action/favorites/remove/' + $actionParam);
            $link.attr('class', '.elgg-menu-item-favorite .favorites-remove');
            $link.children('span').attr('class','elgg-icon elgg-icon-star');
            $link.unbind('click');
            $link.click(elgg.favorites.remove);
        }
    });
};

elgg.favorites.remove = function(event) {
	event.preventDefault();
    console.log('favorites remove');

	var $link = $(this);
    var $actionParam = $link.attr("href").split("/").pop();
	var $postGuid = $link.attr("href").match(/guid=([^&]+)/)[1];
    console.log($postGuid);

    elgg.action('favorites/remove', {
        data: {
            guid: $postGuid
        },
        success: function(json) {
            console.log('yes removed');
            $link.attr("href", '<?php echo $site_url; ?>action/favorites/add/' + $actionParam);
            $link.attr('class', '.elgg-menu-item-favorite .favorites-add');
            $link.children('span').attr('class','elgg-icon elgg-icon-star-empty');
            $link.unbind('click');
            $link.click(elgg.favorites.add);
        }
    });
};

elgg.register_hook_handler('init', 'system', elgg.favorites.init);

