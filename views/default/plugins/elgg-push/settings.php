<?php
/**
 * PuSH plugin settings
 * @package PuSH
 */

?>
<p>
	<label for="push_hub"><?php echo elgg_echo('push:hub:label'); ?></label>
	<?php echo elgg_view('input/text', array('internalname' => 'params[hub]', 'value' =>  $vars['entity']->hub)); ?>
	<?php echo elgg_view('output/longtext', array('value' => elgg_echo('push:hub:details'))); ?>
</p>
