<?php
/**
 * Register the ElggAssembly class for the object/assembly subtype
 */

if (get_subtype_id('object', 'assembly')) {
        update_subtype('object', 'assembly', 'ElggAssembly');
} else {
        add_subtype('object', 'assembly', 'ElggAssembly');
}

