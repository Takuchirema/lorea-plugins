<?php
/**
 * Elggman plugin settings
 * 
 * @package Elggman
 */

?>
<div>
    <br /><label><?php echo elgg_echo('elggman:mailname'); ?></label><br />
    <?php echo elgg_view('input/text',array('name' => 'params[mailname]', 'value' => $vars['entity']->mailname,)); ?>
</div>
