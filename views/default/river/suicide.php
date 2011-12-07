<?php

$annotation_id = $vars['item']->annotation_id;

if ($annotation_id != 2010) {
	$annotation = elgg_get_annotation_from_id($annotation_id);
	
	$value   = explode(',',$annotation->value);
	$name    = $value[0];
	$friends = (int)$value[1];
	
	if($friends == 0){
		$excerpt = "suicide:nocares";
	}elseif($friends < 5){
		$excerpt = "suicide:autist";
	}elseif($friends < 10){
		$excerpt = "suicide:lonely";
	}elseif($friends < 50){
		$excerpt = "suicide:normal";
	}elseif($friends < 100){
		$excerpt = "suicide:popular";
	}elseif($friends < 200){
		$excerpt = "suicide:respected";
	}else{
		$excerpt = "suicide:godlike";
	}
	
	echo elgg_view('river/elements/layout', array(
		'item' => $vars['item'],
		'message' => elgg_echo($excerpt, array($text[0])),
	));
}
