<?php
/**
 * Tag Dashboards English language translation
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
$english = array(
	
	// Generic
	'tagdashboards' => 'Tag Dashboards',
	'tagdashboard' => 'Tag Dashboard',
	'tagdashboards:add' => 'Start New Tag Dashboard',
	'item:object:tagdashboard' => 'Tag Dashboard',
	'tagdashboard:enablegroup' => 'Enable group tag dashboards',
	'tagdashboards:none' => 'No Tag Dashboards',
	'profile:tagportfolio' => 'Tag Portfolio',
	
	'admin:tagdashboards' => 'Tag dashboards',
	'admin:tagdashboards:subtypes' => 'Entity Settings',
	
	// Page titles 
	'tagdashboards:title:search' => 'Tag Dashboard Search',
	'tagdashboards:title:edit' => 'Edit Tag Dashboard',
	'tagdashboards:title:adminsettings' => 'Tag Dashboard Settings',
	'tagdashboards:title:groupbyactivity' => 'Content grouped by student activity',
	'tagdashboards:title:activitytag' => 'Searched tag, grouped by activity',
	'tagdashboards:title:custom' => 'Searched tag, grouped by custom',
	'tagdashboards:title:owneddashboards' => '%s\'s Tag Dashboards',
	
	// Menu items
	'tagdashboards:menu:alltagdashboards' => 'Tag Dashboards',
	'tagdashboards:menu:friendstagdashboards' => 'Friend\'s Tag Dashboards',

	// Labels 
	'tagdashboards:label:submitsearch' => 'GO',
	'tagdashboards:label:subtypesheading' => 'Select Tag Dashboard Subtypes',
	'tagdashboards:label:contenttypes' => 'Show these content types',
	'tagdashboards:label:subtype_heading' => 'Entity Subtype',
	'tagdashboards:label:enabled_heading' => 'Enabled',
	'tagdashboards:label:subtypes_settings_submit' => 'Submit Subtype Settings',
	'tagdashboards:label:title' => 'Title',
	'tagdashboards:label:description' => 'Description',
	'tagdashboards:label:tags' => 'Tags',
	'tagdashboards:label:save' => 'Save',
	'tagdashboards:label:refresh' => 'Refresh',
	'tagdashboards:label:saveform' => 'Edit Options',
	'tagdashboards:label:showsave' => 'Show Save Form',
	'tagdashboards:label:hidesave' => 'Hide Save Form',
	'tagdashboards:label:grouptags' => 'Group tag dashboards', 
	'tagdashboards:label:deleteconfirm' => 'Are you sure you want to delete this Tag Dashboard?',
	'tagdashboards:label:submitted_by' => 'Submitted by %s',
	'tagdashboards:label:leaveacomment' => 'Leave a Comment ',
	'tagdashboards:label:viewfull' => 'View full post', 
	'tagdashboards:label:searchtag' => 'Search Tag',
	'tagdashboards:label:contentview' => 'Content View',
	'tagdashboards:label:timelineview' => 'Timeline View',
	'tagdashboards:label:groupbyactivity' => 'View by activity',
	'tagdashboards:label:search' => 'Search tag',
	'tagdashboards:label:customtags' => 'Group by tags',
	'tagdashboards:label:activity' => 'Group by role',
	'tagdashboards:label:subtype' => 'Group by subtype',
	'tagdashboards:label:grouping' => 'Grouping Settings',
	'tagdashboards:label:filter' => 'Filter Settings',
	'tagdashboards:label:filterowner' => 'Show content from selected users',
	'tagdashboards:label:filterdate' => 'Show content created within date range',
	'tagdashboards:label:clear' => 'Clear',


	// Descriptions
	'tagdashboards:description:subtype' => 'Grouping by subtype will group content matching your search term by the subtypes selected above.',
	'tagdashboards:description:activity' => 'Grouping by role will group content matching your search term by these tags based on Alan November\'s 6 jobs for students::<br /><br />
	<ul>
		<li>research - Researchers</li>
		<li>curriculum - Curriculum Reviewers</li>
		<li>collabco - Collaboration Coordinators</li>
		<li>tutorial - Tutorial Designers</li>
		<li>society - Contributing to Society</li>
		<li>scribe - Official Scribes</li>
	</ul><br />
	For more information on the 6 jobs for students, see the article on <a href="http://novemberlearning.com/wp-content/uploads/2009/02/students-as-contributors.pdf">novemberlearning.com</a>',
	'tagdashboards:description:custom' => 'Grouping by tag will group content matching your search term and by the tags you enter below.',

	'tagdashboards:description:tagportfolio' => 'Your tag portfolio contains all of your content organized by your chosen tags.',

	// Activities
	'tagdashboards:activity:research' => 'Researcher',
	'tagdashboards:activity:curriculum' => 'Curriculum Reviewer',
	'tagdashboards:activity:collabco' => 'Collaboration Coordinator',
	'tagdashboards:activity:tutorial' => 'Tutorial Designers',
	'tagdashboards:activity:society' => 'Contributors To Society',
	'tagdashboards:activity:scribe' => 'Official Scribes',

	// River
	'tagdashboards:river:tagdashboard:create' => '%s created a Tag Dashboard titled ',

	
	// Messages
	'tagdashboards:success:setenabledsubtypes' => 'Enabled subtypes set',
	'tagdashboards:success:save' => 'Succesfully saved Tag Dashboard',
	'tagdashboards:success:saveportfolio' => 'Succesfully saved Tag Portfolio',
	'tagdashboards:success:delete' => 'Tag Dashboard succesfully deleted',
	'tagdashboards:error:save' => 'Error saving Tag Dashboard',
	'tagdashboards:error:delete' => 'There wasn an error deleting the Tag Dashboard',
	'tagdashboards:error:notfound' => 'Tag Dashboard not found',
	'tagdashboards:error:requiredfields' => 'One or more required fields are missing',
	'tagdashboards:error:noportfolio' => '%s has not yet set up his/her Tag Portfolio',


	
	// Other content


);

add_translation('en',$english);
