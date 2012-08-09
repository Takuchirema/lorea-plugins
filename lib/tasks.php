<?php

/**
 * Prepare the add/edit form variables
 *
 * @param ElggObject $tasklist
 * @return array
 */
function tasklist_prepare_form_vars($tasklist = null, $list_guid = 0) {

	// input names => defaults
	$values = array(
		'title' => '',
		'description' => '',
		'enddate' => '',
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $tasklist,
		'list_guid' => $list_guid,
	);

	if ($tasklist) {
		foreach (array_keys($values) as $field) {
			if (isset($tasklist->$field)) {
				$values[$field] = $tasklist->$field;
			}
		}
	}

	if (elgg_is_sticky_form('tasklist')) {
		$sticky_values = elgg_get_sticky_values('tasklist');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('tasklist');

	return $values;
}

/**
 * Prepare the add/edit form variables
 *
 * @param ElggObject $task
 * @return array
 */
function task_prepare_form_vars($task = null, $list_guid = 0) {

	// input names => defaults
	$values = array(
		'title' => get_input('title', ''),
		'description' => '',
		'priority' => '',
		'access_id' => ACCESS_DEFAULT,
		'write_access_id' => ACCESS_DEFAULT,
		'tags' => '',
		'container_guid' => elgg_get_page_owner_guid(),
		'guid' => null,
		'entity' => $task,
		'list_guid' => $list_guid,
		'referer_guid' => get_input('referer_guid', ''),
	);

	if ($task) {
		foreach (array_keys($values) as $field) {
			if (isset($task->$field)) {
				$values[$field] = $task->$field;
			}
		}
	}

	if (elgg_is_sticky_form('task')) {
		$sticky_values = elgg_get_sticky_values('task');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}

	elgg_clear_sticky_form('task');
	
	if ($referer = get_input('referer')) {
		$link = elgg_view('output/url', array(
			'href' => $referer,
			'text' => elgg_echo('tasks:this:moreinfo:here'),
		));
		$values['description'] .= "<p>" . elgg_echo('tasks:this:moreinfo', array($link)) . "</p>";
	}

	return $values;
}


function tasks_get_entities($options) {
	$default = array(
		'type' => 'object',
		'subtype' => 'task',
	);
	if (!isset($options['metadata_name_value_pairs']) || !is_array($options['metadata_name_value_pairs'])) {
		$options['metadata_name_value_pairs'] = array();
	}
	if (isset($options['list_guid'])) {
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'list_guid',
			'value' => $options['list_guid'],
		);
		unset($options['parent_guid']);
	}
	if (isset($options['status'])) {
		$options['metadata_name_value_pairs'][] = array(
			'name' => 'status',
			'value' => $options['status'],
		);
		unset($options['status']);
	}
	
	$options = array_merge($default, $options);
	return elgg_get_entities_from_metadata($options);
}

function tasks_get_actions_from_state($state){
	switch($state) {
		
		case 'new':
		case 'unassigned':
		case 'reopened':
			$actions = array(
				'assign',
				'assign_and_activate',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'assigned':
			$actions = array(
				'activate',
				'leave',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'active':
			$actions = array(
				'deactivate',
				'leave',
				'mark_as_done',
				'close',
			);
			break;
			
		case 'done':
		case 'closed':
			$actions = array(
				'reopen',
			);
			break;
			
	}
	
	return $actions;
}

function tasks_prepare_radio_options($state) {
	
	$actions = tasks_get_actions_from_state($state);
	
	$actions_labels = array(
		elgg_echo("tasks:state:action:noaction", array($state)) => '',
	);
	
	foreach($actions as $action) {
		$actions_labels[elgg_echo("tasks:state:action:$action")] = $action;
	}
	
	return $actions_labels;
}

function tasks_register_actions_menu($task) {
	foreach (tasks_get_actions_from_state($task->status) as $action) {
		$state = tasks_get_state_from_action($action);
		$action_url = "action/tasks/comments/add?" . http_build_query(array(
			'entity_guid' => $task->guid,
			'state_action' => $action,
		));
		$action_url = elgg_add_action_tokens_to_url($action_url);
		$item = new ElggMenuItem($action, elgg_echo("tasks:state:action:$action"), $action_url);
		elgg_register_menu_item('tasks_hover', $item);
	}
}
				
function tasks_get_state_from_action($action){
	$actions_states = array(
		'assign' => 'assigned',
		'leave' => 'unassigned',
		'activate' => 'active',
		'deactivate' => 'assigned',
		'assign_and_activate' => 'active',
		'mark_as_done' => 'done',
		'close' => 'closed',
		'reopen' => 'reopened',
	);
	return $actions_states[$action];
}

function tasks_get_user_active_task ($user_guid) {
	return array_shift(elgg_get_entities_from_metadata(array(
		'metadata_name' => 'status',
		'metadata_value' => 'active',
		'owner_guid' => $user_guid,
	)));
}

/**
 * Register the navigation menu
 * 
 * @param ElggEntity $container Container entity for the tasks
 */
function tasks_register_navigation_tree($container) {
	if (!$container) {
		return;
	}

	$tasklists_top = elgg_get_entities(array(
		'type' => 'object',
		'subtype' => 'tasklist_top',
		'container_guid' => $container->getGUID(),
		'limit' => 0,
	));

	if (!$tasklists_top) {
		return;
	}

	foreach ($tasklists_top as $tasklist) {
		elgg_register_menu_item('tasks_nav', array(
			'name' => $tasklist->getGUID(),
			'text' => $tasklist->title,
			'href' => $tasklist->getURL(),
		));

		$stack = array();
		array_push($stack, $tasklist);
		while (count($stack) > 0) {
			$list = array_pop($stack);
			$tasks = tasks_get_entities(array(
				'limit' => 0,
			));

			if ($tasks) {
				foreach ($tasks as $task) {
					elgg_register_menu_item('tasks_nav', array(
						'name' => $task->getGUID(),
						'text' => $task->title,
						'href' => $task->getURL(),
						'list_name' => $list->getGUID(),
					));
					array_push($stack, $task);
				}
			}
		}
	}
}

