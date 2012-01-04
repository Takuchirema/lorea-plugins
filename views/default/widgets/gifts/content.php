<?php
/**
 * Elgg gifts widget
 *
 * @package ElggGifts
 */

$gifts = gifts_get_registered_gifts();
$owner = elgg_get_page_owner_entity();

echo '<ul class="elgg-gallery">';
foreach ($gifts as $gift_name => $gift_img) {
	$gift_name = elgg_echo("gifts:gift:$gift_name");
	$give = elgg_echo("gifts:give", array($gift_name, $owner->name));
	echo '<li class="elgg-item">';
	echo '<a title="'.$give.'" href=""><img class="gift" src="'.$gift_img.'" alt="'.$gift_name.'" /></a>';
	echo '</li>';	
}
echo '</ul>';

?>

<script type ="text/javascript">

$(function(){
	$('img.gift').mouseover(function(){
		$('img.gift').stop().fadeTo(1000, 0.3);
		$(this).stop().fadeTo(1000, 1);
	}).mouseout(function(){
		$('img.gift').stop().fadeTo(1000, 1);
	});
});

</script>
