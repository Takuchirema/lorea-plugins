<?php
	$site_url = elgg_get_site_url();
	$label = elgg_echo("ostatus:subscribeto:enter");

	$body = elgg_view('input/text', array('internalname' => 'uri', 'size' => '40'));
	$body .= "<p>".elgg_echo("ostatus:subscribeto:enter:description")."</p>";
	$body .= elgg_view("input/submit", array('value'=>'Confirm', 'internalname'=>'submit'));

?>
<label><?php echo $label; ?></label>
<form action='<?php echo $site_url; ?>ostatus/subscribe' method='get'>
	<?php echo $body; ?>
</form>
