<?php
/**
 * Elgg gifts widget
 *
 * @package ElggGifts
 */

$gifts = gifts_get_registered_gifts();

echo '<ul class="elgg-gallery">';
foreach ($gifts as $gift_name => $gift_img) {
	echo '<li class="elgg-item">';
	echo '<a title="'.elgg_echo("gifts:gift:$gift_name").'" href=""><img src="'.$gift_img.'" /></a>';
	echo '</li>';	
}
echo '</ul>';
