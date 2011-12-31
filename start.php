<?php
 /**
 * AudioHTML5 - an audio files player
 * A simple plugin to play audio files on the page
 *
 * @package ElggAudioHTML5
 */

elgg_register_event_handler('init', 'system', 'audio_html5_init');

function audio_html5_init() {
	
	elgg_extend_view('css/elgg', 'audio_html5/css');
	
}
