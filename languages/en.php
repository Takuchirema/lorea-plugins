<?php
/**
 * Assembly English language file.
 *
 */

$english = array(
	'assemblies' => 'Assemblies',
	'assemblies:assembly' => 'Assembly',
	'item:object:assembly' => 'Assemblies',
	'assemblies:none' => 'No assemblies',

	'assemblies:title:all_assemblies' => 'All site assemblies',

	'assemblies:group' => 'Group assemblies',
	'assemblies:enableassemblies' => 'Enable group assemblies',

	// General properties
	'assemblies:periodicity' => 'Periodicity',
	'assemblies:chat' => 'Chat url',
	'assemblies:streaming' => 'Streaming url',
	'assemblies:voip' => 'Voip url',

	// Assembly fields
	'assemblies:assembly:title' => 'Title',
	'assemblies:assembly:description' => 'Description',
	'assemblies:assembly:date' => 'Date',
	'assemblies:assembly:access_id' => 'Access',

	// Group widget
	'assemblies:link:view' => 'View',
	'assemblies:link:edit' => 'Edit',
	'assemblies:info' => 'Info',
	'assemblies:info:when' => 'When',
	'assemblies:info:where' => 'Where',
	'assemblies:agenda' => 'Agenda',
	'assemblies:minutes' => 'Minutes',

	// Crud
	'crud:assembly:nochildren' => 'There are no points created for this assembly',
	'crud:assembly:addchild' => 'Add',
	

	// Editing
	'assembly:add' => 'Add assembly call',
	'assembly:edit' => 'Edit assembly call',

	// messages
	'assemblies:message:saved' => 'Assembly call saved.',
	'assemblies:error:cannot_save' => 'Cannot save assembly call.',
	'assemblies:error:cannot_write_to_container' => 'Insufficient access to save assembly to group.',
	'assemblies:message:deleted_post' => 'Assembly deleted.',
	'assemblies:error:cannot_delete_post' => 'Cannot delete assembly.',
	'assemblies:error:missing:title' => 'Please enter a assembly title!',
	'assemblies:error:cannot_edit' => 'This assembly may not exist or you may not have permissions to edit it.',

	// river
	'river:create:object:assembly' => '%s published a assembly call %s',
	'river:comment:object:assembly' => '%s commented on the assembly %s',

	// notifications
	'assemblies:newcall' => 'A new assembly call',
	'assemblies:notification' =>
'
%s made a new assembly call.

%s
%s

View and suggest new proposals on the new assembly call:
%s
',

	// widget
	'assemblies:widget:description' => 'Display latest assembly calls',
	'assemblies:moreassemblies' => 'More assembly calls',
	'assemblies:numbertodisplay' => 'Number of assembly calls to display',
	'assemblies:nocalls' => 'No assembly calls',
);

add_translation('en', $english);
