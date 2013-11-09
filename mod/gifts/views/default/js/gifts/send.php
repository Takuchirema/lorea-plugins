<?php
/**
 * Select gifts effects
 *
 * @package ElggGifts
 */
?>
elgg.provide('elgg.gifts');

elgg.gifts.init = function() {
	$('a.gift').mouseover(function(){
		// If no a gift clicked, highlight gift
		if($('#gifts_note input[name=gift]').val() == ""){
			$('a.gift').stop().fadeTo(1000, 0.3);
			$(this).stop().fadeTo(1000, 1);
		}
	}).mouseout(function(){
		// If no a gift clicked, unhighlight all gifts
		if($('#gifts_note input[name=gift]').val() == ""){
			$('a.gift').stop().fadeTo(1000, 1);
		}
	}).click(function(){
		
		// Highlight clicked gift
		$('a.gift').stop().fadeTo(1000, 0.3);
		$(this).stop().fadeTo(1000, 1);
		
		// Get gift and name
		gift = $(this).attr('id').replace('gift-', '');
		gift_name = elgg.echo('gifts:gift:'+gift).toLowerCase();
		
		// Set them in form
		$('#gifts_note').slideDown('fast');
		$('#gifts_note input[name=gift]').val(gift);
		$('#gifts_note label').html(elgg.echo('gifts:enclose', [gift_name]));
		$('#gifts_note input[name=note]').focus();
		
		// Do not follow link
		return false;
	});
};

elgg.register_hook_handler('init', 'system', elgg.gifts.init);
