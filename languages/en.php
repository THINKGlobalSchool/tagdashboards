<?php
/**
 * Ubertags English language translation
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$english = array(
	
	// Generic
	'ubertags' => 'Ubertags',
	'ubertag' => 'Ubertags',
	'ubertag:new' => 'Start New Ubertag',
	'item:object:ubertag' => 'Ubertags',
	
	// Page titles 
	'ubertags:title:search' => 'Ubertags Search',
	'ubertags:title:edit' => 'Edit Ubertag',
	'ubertags:title:adminsettings' => 'Ubertags Settings',
	'ubertags:title:groupbyactivity' => 'Content grouped by student activity',
	'ubertags:title:activitytag' => 'Searched tag, grouped by activity',
	'ubertags:title:custom' => 'Searched tag, grouped by custom',
	
	// Menu items
	'ubertags:menu:allubertags' => 'All Ubertags',
	'ubertags:menu:yourubertags' => 'Your Ubertags',
	'ubertags:menu:friendsubertags' => 'Friend\'s Ubertags',


	// Labels 
	'ubertags:label:submitsearch' => 'GO',
	'ubertags:label:subtypesheading' => 'Select Ubertags Subtypes',
	'ubertags:label:contenttypes' => 'Show these content types',
	'ubertags:label:subtype_heading' => 'Entity Subtype',
	'ubertags:label:enabled_heading' => 'Enabled',
	'ubertags:label:subtypes_settings_submit' => 'Submit Subtype Settings',
	'ubertags:label:title' => 'Title',
	'ubertags:label:description' => 'Description',
	'ubertags:label:tags' => 'Tags',
	'ubertags:label:save' => 'Save',
	'ubertags:label:refresh' => 'Refresh',
	'ubertags:label:saveform' => 'Edit Options',
	'ubertags:label:showsave' => 'Show Save Form',
	'ubertags:label:hidesave' => 'Hide Save Form',
	'ubertags:label:grouptags' => 'Group Ubertags', 
	'ubertags:label:deleteconfirm' => 'Are you sure you want to delete this Ubertag?',
	'ubertags:label:submitted_by' => 'Submitted by %s',
	'ubertags:label:leaveacomment' => 'Leave a Comment ',
	'ubertags:label:viewfull' => 'View full post', 
	'ubertags:label:searchtag' => 'Search Tag',
	'ubertags:label:contentview' => 'Content View',
	'ubertags:label:timelineview' => 'Timeline View',
	'ubertags:label:groupbyactivity' => 'View by activity',
	'ubertags:label:search' => 'Search tag',
	'ubertags:label:customtags' => 'Group by tags',
	'ubertags:label:activity' => 'Group by role',
	'ubertags:label:subtype' => 'Group by subtype',
	'ubertags:label:grouping' => 'Grouping Settings',

	// Descriptions
	'ubertags:description:subtype' => 'Grouping by subtype will group content matching your search term by the subtypes selected above.',
	'ubertags:description:activity' => 'Grouping by role will group content matching your search term by these tags based on Alan November\'s 6 jobs for students::<br /><br />
	<ul>
		<li>research - Researchers</li>
		<li>curriculum - Curriculum Reviewers</li>
		<li>collabco - Collaboration Coordinators</li>
		<li>tutorial - Tutorial Designers</li>
		<li>society - Contributing to Society</li>
		<li>scribe - Official Scribes</li>
	</ul><br />
	For more information on the 6 jobs for students, see the article on <a href="http://novemberlearning.com/wp-content/uploads/2009/02/students-as-contributors.pdf">novemberlearning.com</a>',
	'ubertags:description:custom' => 'Grouping by tag will group content matching your search term and by the tags you enter below.',

	// Activities
	'ubertags:activity:research' => 'Researcher',
	'ubertags:activity:curriculum' => 'Curriculum Reviewer',
	'ubertags:activity:collabco' => 'Collaboration Coordinator',
	'ubertags:activity:tutorial' => 'Tutorial Designers',
	'ubertags:activity:society' => 'Contributors To Society',
	'ubertags:activity:scribe' => 'Official Scribes',

	// River
	'ubertags:river:ubertag:create' => '%s created an Ubertag titled ',

	
	// Messages
	'ubertags:success:setenabledsubtypes' => 'Enabled subtypes set',
	'ubertags:success:save' => 'Succesfully saved Ubertag',
	'ubertags:success:delete' => 'Ubertag succesfully deleted',
	'ubertags:error:save' => 'Error saving Ubertag',
	'ubertags:error:delete' => 'There wasn an error deleting the Ubertag',
	'ubertags:error:notfound' => 'Ubertag not found',
	'ubertags:error:requiredfields' => 'One or more required fields are missing',


	
	// Other content


);

add_translation('en',$english);
