<?php
/**
 * AudioHTML5 audio player
 * @package ElggAudioHTML5
 */

$audio_url = elgg_get_site_url() . "mod/file/download.php?file_guid={$vars['file_guid']}";

?>

<div class="audio-html5">
	<audio src="<?php echo $audio_url; ?>" controls width="90" height="50"></audio>            
</div>
