<?php
/**
         * Elgg powered plugin
         * 
         * @package
         * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
         * @author lorea
         * @copyright lorea
         * @link http://lorea.cc
         */

 	function powered_init(){
		elgg_extend_view('css','powered/css');
		// Extend footer
                extend_view("footer/analytics", "powered/footer");
	}
register_elgg_event_handler('init','system','powered_init');

?>
