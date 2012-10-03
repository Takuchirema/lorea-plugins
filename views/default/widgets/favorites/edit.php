<?php
if (!isset($vars['entity']->pages_num)) {
    $vars['entity']->pages_num = 4;
}

$params = array(
      'name' => 'params[pages_num]',
      'value' => $vars['entity']->pages_num,
      'options' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10),
);
$dropdown = elgg_view('input/dropdown', $params);

?>
<div>
      <?php echo elgg_echo('favorites:numbertodisplay'); ?>:
      <?php echo $dropdown; ?>
</div>
