<?php
 /**
 * AudioHTML5 - an audio files player
 * A simple plugin to play audio files on the page
 *
 * @package ElggAudioHTML5
 */

elgg_register_event_handler('init', 'system', 'audio_html5_init');

function audio_html5_init() {
	
	// File plugin do not get OGG files as audio, because its mime type is application/ogg. This will solve this inconvenience.
	elgg_register_plugin_hook_handler('file_simple_type', 'object', 'audio_html5_ogg_general_type');
	elgg_register_plugin_hook_handler('file:icon:url', 'override', 'audio_html5_ogg_icon_url_override');
	
	elgg_extend_view('css/elgg', 'audio_html5/css');
	
}

function audio_html5_ogg_general_type($hook, $type, $return, $params) {
	if($params['entity']->mimetype == 'application/ogg') {	
		$return = "audio";
	}
	return $return;
}

function audio_html5_ogg_icon_url_override($hook, $type, $return, $params) {
	if($params['entity']->mimetype == 'application/ogg') {
		if ($params['size'] == 'large') {
			$ext = '_lrg';
		} else {
			$ext = '';
		}		
		$return = "mod/file/graphics/icons/music{$ext}.gif";
	}
	return $return;
}
