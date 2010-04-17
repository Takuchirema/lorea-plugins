<?php
	$entity = $vars['entity'];
	$form_body = '<label>'.elgg_echo('microthemes:title').'<label>';
	$values = array();
	$assign_to = get_input('assign_to');
	$opt_values = array();
	if ($entity) {
		$form_body .= elgg_view("input/hidden", array(
                        'internalname' => 'entity_guid',
			'value' => $entity->getGUID()));
		$bgcolor = $entity->bg_color;
		$topbar_color = $entity->topbar_color;
		$title = $entity->title;
		if ($entity->hidesitename === 1)
			array_push($opt_values, 'hidesitename');
		if ($entity->translucid_page === 1)
			array_push($opt_values, 'translucid_page');
		if ($entity->repeatx === 1)
			array_push($values, 'repeatx');
		if ($entity->repeaty === 1)
			array_push($values, 'repeaty');
		if ($entity->bg_alignment)
			$align = $entity->bg_alignment;
		else
			$align = 'left';
		if ($entity->height === null)
			$height = 120;
		else
			$height = (int)$entity->height;
			
	}
	else {
		$bgcolor = '#000000';
		$topbar_color = '#000000';
		$height = 120;
		$title = '';
		$align = 'left';
	}

	$form_body .= elgg_view("input/hidden", array(
			'internalname' => 'assign_to', 'value'=>$assign_to));
	$form_body .= elgg_view("input/text", array(
			'internalname' => 'title', 'value'=>$title));

	// backgroud color
	$form_body .= '<label>'.elgg_echo('microthemes:color').'<label>';
	$form_body .= elgg_view("input/text", array(
			'internalname' => 'bg_color', 'value'=>$bgcolor));

	// topbar color
	$form_body .= '<label>'.elgg_echo('microthemes:topbar_color').' <label>';
	$form_body .= elgg_view("input/text", array(
			'internalname' => 'topbar_color',
			'value'=>$topbar_color));

	// banner
	$form_body .= '<p><label>'.elgg_echo('microthemes:banner').' <label>';
	$form_body .= elgg_view("input/file", array(
			'internalname' => 'banner_file')).'</p>';
	$form_body .= '<label>'.elgg_echo('microthemes:headerheight').'<label>';
	$form_body .= elgg_view("input/text", array(
			'internalname' => 'height', 'value'=>$height));
	$form_body .= '<p>';
	$form_body .= '<p><label>'.elgg_echo('microthemes:bgrepetition').' <label><br />';
	$form_body .= elgg_view("input/checkboxes", array(
			'options' => array(elgg_echo('microthemes:repeatx')=>'repeatx',
			elgg_echo('microthemes:repeaty')=>'repeaty'),
			'internalname'=>'repeat',
			'value' => $values));
	$form_body .= '</p>';
	$form_body .= '<p><label>'.elgg_echo('microthemes:alignment').' <label><br />';
	$form_body .= elgg_view("input/radio", array('value'=>$align,
				'internalname' => 'alignment',
				'options'=>array(
				elgg_echo('microthemes:left')=>'left',
				elgg_echo('microthemes:center')=>'center',
				elgg_echo('microthemes:right')=>'right')));
	$form_body .= '</p>';
	$form_body .= '<p>';
	$form_body .= '<p><label>'.elgg_echo('microthemes:options').' <label><br />';
	$form_body .= elgg_view("input/checkboxes", array(
			'options' => array(
			elgg_echo('microthemes:hidesitename')=>'hidesitename',
			elgg_echo('microthemes:translucid_page')=>'translucid_page'),
			'internalname'=>'options',
			'value' => $opt_values));
	$form_body .= '</p>';

	// footer
	//$form_body .= '<label>'.elgg_echo('microthemes:footer').'<label>';
	//$form_body .= elgg_view("input/file", array(
	//		'internalname' => 'footer_file'));

	// submit
	$form_body .= elgg_view('input/submit', array('internalname' => 'submit', 'value' => elgg_echo('microthemes:publish')));

	echo elgg_view('input/form', array(
			'body' => $form_body,
			'method' => 'post',
			'enctype' => 'multipart/form-data',
			'action'=>$vars['url'].'action/microthemes/edit'));
?>
