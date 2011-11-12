<?php
/**
 * Tasks languages
 *
 * @package ElggTasks
 */

$english = array(

	/**
	 * Menu items and titles
	 */

	'tasks' => "Tasks",
	'tasks:owner' => "%s's tasks",
	'tasks:friends' => "Friends' tasks",
	'tasks:all' => "All site tasks",
	'tasks:add' => "Add task",

	'tasks:group' => "Group tasks",
	'groups:enabletasks' => 'Enable group tasks',

	'tasks:edit' => "Edit this task",
	'tasks:delete' => "Delete this task",
	'tasks:view' => "View task",

	'tasks:via' => "via tasks",
	'item:object:tasklist' => 'Task lists',
	'item:object:task' => 'Tasks',
	'tasks:nogroup' => 'This group does not have any tasks yet',
	'tasks:more' => 'More tasks',
	'tasks:none' => 'No tasks created yet',

	/**
	* River
	**/

	'river:create:object:task' => '%s created a task %s',
	'river:create:object:tasklist' => '%s created a task list %s',
	'river:update:object:task' => '%s updated a task %s',
	'river:update:object:tasklist' => '%s updated a task list %s',
	'river:comment:object:task' => '%s commented on a task titled %s',
	'river:comment:object:tasklist' => '%s commented on a task list titled %s',

	/**
	 * Form fields
	 */

	'tasks:title' => 'Task title',
	'tasks:description' => 'Task text',
	'tasks:tags' => 'Tags',
	'tasks:access_id' => 'Who can see this task?',

	/**
	 * Status and error messages
	 */
	'tasks:noaccess' => 'No access to task',
	'tasks:cantedit' => 'You cannot edit this task',
	'tasks:saved' => 'Task saved',
	'tasks:notsaved' => 'Task could not be saved',
	'tasks:error:no_title' => 'You must specify a title for this task.',
	'tasks:delete:success' => 'The task was successfully deleted.',
	'tasks:delete:failure' => 'The task could not be deleted.',

	/**
	 * Task
	 */
	'tasks:strapline' => 'Last updated %s by %s',

	/**
	 * Widget
	 **/

	'tasks:num' => 'Number of tasks to display',
	'tasks:widget:description' => "This is a list of your tasks.",

	/**
	 * Submenu items
	 */
	'tasks:label:view' => "View task",
	'tasks:label:edit' => "Edit task",
	
	/**
	 * Sidebar items
	 */
	'tasks:sidebar:this' => "This list",
	'tasks:sidebar:children' => "List tasks",
	'tasks:sidebar:parent' => "List",

	'tasks:newchild' => "Create a task in this list",
	'tasks:backtoparent' => "Back to '%s'",
);

add_translation("en", $english);
