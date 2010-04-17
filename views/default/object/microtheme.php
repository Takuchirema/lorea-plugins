<?php
/**
 * ElggEntity default view.
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 */

$assign_to = get_input("assign_to");

if ($vars['full']) {
	echo elgg_view('export/entity', $vars);
} else {
	$iconurl = $CONFIG->wwwroot.'mod/microthemes/graphics/icon.php?size=small&mode=banner&object_guid='.$vars['entity']->guid;
	$icon = "<img src='".$iconurl."' />";
	$iconurl = $CONFIG->wwwroot.'mod/microthemes/graphics/icon.php?size=medium&mode=banner&object_guid='.$vars['entity']->guid;
	$mediumicon = "<img src='".$iconurl."' />";
	$entity =  $vars['entity'];
	if ($entity->title)
		$title = $entity->title;
	else
		$title = $entity->topic;

	$controls = "";
	if ($vars['entity']->canEdit() || isadminloggedin()) {
		$action_ref = "{$vars['url']}action/microthemes/delete?guid={$vars['entity']->guid}";
		if ($assign_to)
			$action_ref .= '&assign_to='.$assign_to;
		$delete = elgg_view('output/confirmlink', array(
			'href' => $action_ref, 
			'text' => elgg_echo('delete')
		));
		$controls .= " ($delete)";
		$action_ref = "{$vars['url']}pg/microthemes/edit/{$vars['entity']->guid}";
		if ($assign_to)
			$action_ref .= '?assign_to='.$assign_to;
		$edit = elgg_view('output/url', array(
			'href' => $action_ref,
			'text' => elgg_echo('edit')
		));
		$controls .= " ($edit)";

	}
	if ($vars['entity']->canEdit() || isadminloggedin() || $assign_to) {
		if ($vars['entity']->getGUID() != get_entity($assign_to)->microtheme) {
			$action_ref = "{$vars['url']}action/microthemes/choose?guid={$vars['entity']->guid}";
			if ($assign_to)
				$action_ref .= '&assign_to='.$assign_to;
			$pars =  array(
				'href' => $action_ref,
				'text' => elgg_echo('microthemes:choose')
			);
				
			$choose = elgg_view('output/confirmlink', $pars);
			$controls .= " ($choose)";
		}

	}
	if (isadminloggedin()) {
		$site = get_site_by_url($vars['url']);
		$action_ref = "{$vars['url']}action/microthemes/choose?guid={$vars['entity']->guid}";
		$action_ref .= '&assign_to='.$site->getGUID();
		$pars =  array(
                        'href' => $action_ref,
                        'text' => elgg_echo('microthemes:choosesite')
                );
			
		$choose = elgg_view('output/confirmlink', $pars);
		$controls .= " ($choose)";

	}

	$info = "<div><p><b><a href=\"" . $vars['entity']->getUrl() . "\">" . $title . "</a></b> $controls </p></div>";
	$info .= '<b>topbar:</b> ' ."<div style='width:10px; height:10px; display:inline-block; background-color:".$vars['entity']->topbar_color.";'></div>    ";
	$info .= '<b>background:</b> ' ."<div style='width:10px; height:10px; display:inline-block; background-color:".$vars['entity']->bg_color.";'></div>";;

	if (get_input('search_viewtype') == "gallery") {
		$icon = "";
	}

	$owner = $vars['entity']->getOwnerEntity();
	$ownertxt = elgg_echo('unknown');
	if ($owner) {
		$ownertxt = "<a href=\"" . $owner->getURL() . "\">" . $owner->name ."</a>";
	}

	$info .= "<div>".sprintf(elgg_echo("entity:default:strapline"),
		friendly_time($vars['entity']->time_created),
		$ownertxt
	);

	$info .= "</div>";

	$info = "<span title=\"" . "\">$info</span>";
	$icon = "<span title=\"" . "\">$icon</span>";

	echo elgg_view_listing($icon, $info);
}
