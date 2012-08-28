<?php
/**
 * Assembly English language file.
 *
 */

$english = array(
	'assemblies' => 'Assemblies',
	'assemblies:assembly' => 'Assembly',
	'item:object:assembly' => 'Assemblies',
	'item:object:agenda_point' => 'Agenda points',
	'assemblies:none' => 'No assemblies',
	'assemblies:assembly:none' => 'No assemblies',
	'assemblies:none-previous' => 'No previous assemblies',

	'assemblies:title:all_assemblies' => 'All site assemblies',
	'assembly:edit_general' => 'Edit defaults',

	'assemblies:group' => 'Group assembly',
	'assemblies:assembly:group' => 'Group assemblies',
	'assemblies:agenda_point:group' => 'Group minutes',
	'assemblies:enableassemblies' => 'Enable group assemblies',
	'assemblies:next' => 'Next assembly',

	// General properties
	'assemblies:assembly_location' => 'Default location',
	'assemblies:periodicity' => 'Periodicity',
	'assemblies:chat' => 'Chat url',
	'assemblies:streaming' => 'Streaming url',
	'assemblies:voip' => 'Voip url',
	'assemblies:location' => 'Default location',

	// Assembly fields
	'assemblies:assembly:title' => 'Title',
	'assemblies:assembly:description' => 'Description',
	'assemblies:assembly:date' => 'Date',
	'assemblies:assembly:location' => 'Location',
	'assemblies:assembly:access_id' => 'Access',
	'assemblies:assembly:children' => 'Agenda points',
	'assemblies:assembly:owner' => 'Created by %s',

	// Agenda points
	'agenda_point:add' => 'Add agenda point',
	'agenda_point:edit' => 'Edit agenda point',
	'assemblies:agenda_point:title' => 'Title',
	'assemblies:agenda_point:description' => 'Description',
	'assemblies:agenda_point:date' => 'Date',
	'assemblies:agenda_point:access_id' => 'Access',
	'assemblies:agenda_point:status' => 'Status',
	'assemblies:agenda_point:tags' => 'Tags',
	'assemblies:agenda_point:mode' => 'Class',
	'assemblies:agenda_point:owner' => 'Proposed by %s',

	// modes
	'assemblies:agenda_point:all' => 'All',
	'assemblies:agenda_point:permanent' => 'Permanent',
	'assemblies:agenda_point:conjunctural' => 'Conjunctural',
	// statuses
	'assemblies:agenda_point:new' => 'New',
	'assemblies:agenda_point:accepted' => 'Accepted',
	'assemblies:agenda_point:discarded' => 'Discarded',
	'assemblies:agenda_point:delayed' => 'Delayed',

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
	'assemblies:message:deleted_assembly' => 'Assembly deleted.',
	'assemblies:error:cannot_delete_assembly' => 'Cannot delete assembly.',
	'assemblies:error:assembly_not_found' => 'Assembly not found.',
	'assemblies:message:deleted_agenda_point' => 'Agenda point deleted.',
	'assemblies:error:cannot_delete_agenda_point' => 'Cannot delete agenda point.',
	'assemblies:error:agenda_point_not_found' => 'Agenda point not found.',
	'assemblies:error:missing:title' => 'Please enter a assembly title!',
	'assemblies:error:cannot_edit' => 'This assembly may not exist or you may not have permissions to edit it.',

	// river
	'river:create:object:assembly' => '%s published an assembly call %s',
	'river:create:object:agenda_point' => '%s published an agenda point %s',
	'river:edited:object:assembly' => '%s edited an assembly call %s',
	'river:edited:object:agenda_point' => '%s edited an agenda point %s',
	'river:comment:object:assembly' => '%s commented on the assembly %s',
	'river:comment:object:agenda_point' => '%s commented on the agenda point %s',

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
