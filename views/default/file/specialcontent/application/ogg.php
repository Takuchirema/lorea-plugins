<?php
/**
 * AudioHTML5 file view override
 * @package ElggAudioHTML5
 */

echo elgg_view('audio_html5/audioplayer', array('file_guid' => $vars['entity']->getGUID()));
