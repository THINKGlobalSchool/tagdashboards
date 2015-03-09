<?php
/**
 * Tag Dashboards save action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 * 
 */

// Get inputs
$guid 				= get_input('guid');
$title 				= get_input('title');
$description 		= get_input('description');
$tags 				= string_to_tag_array(get_input('tags'));
$access 			= get_input('access_id');
$search 			= get_input('search');
$subtypes 			= get_input('subtypes');
$groupby 			= get_input('groupby');	// How are we grouping the content
$custom_tags 		= string_to_tag_array(get_input('custom')); // Custom fields
$container_guid 	= get_input('container_guid', NULL);
$lower_date 		= strtotime(get_input('lower_date', null));
$upper_date 		= strtotime(get_input('upper_date', null));
$column_count		= get_input('columns');
$group_content      = get_input('group_content', null);
$default_view       = get_input('default_view', 'content');

// Trim any trailing commas from search tag
$search = rtrim($search, ',');

// Sticky form
elgg_make_sticky_form('tagdashboards-save-form');
if (!$title) {
	register_error(elgg_echo('tagdashboards:error:requiredfields'));
	forward(elgg_get_site_url() . 'tagdashboards/add#' . $search);
}

// Editing
if ($guid) {
	$entity = get_entity($guid);
	if (elgg_instanceof($entity, 'object', 'tagdashboard') && $entity->canEdit()) {
		$tagdashboard = $entity;
	} else {
		register_error(elgg_echo('tagdashboards:error:save'));
		forward(REFERER);
	}
} else { // New 
	$tagdashboard = new ElggObject();
	$tagdashboard->subtype = 'tagdashboard';
	$tagdashboard->container_guid = $container_guid;
}

$tagdashboard->title = $title;
$tagdashboard->description = $description;
$tagdashboard->tags = $tags;
$tagdashboard->access_id = $access;
$tagdashboard->search = $search;
$tagdashboard->subtypes = serialize($subtypes);
$tagdashboard->groupby = $groupby;
$tagdashboard->custom_tags = $custom_tags;
$tagdashboard->lower_date = $lower_date;
$tagdashboard->upper_date = $upper_date;
$tagdashboard->group_content = $group_content == 'on' ? 1 : 0;
$tagdashboard->default_view = $default_view;

// If we're grouping by users, the 'members' input will be user guids instead of owner guids
if ($groupby == 'users') {
	$user_guids = get_input('members');
	$tagdashboard->user_guids = $user_guids;
} else {
	$owner_guids = get_input('members');
	$tagdashboard->owner_guids = $owner_guids;
}

// If column count is checked, set to 1
if ($column_count) {
	$tagdashboard->column_count = 1;
} else {
	$tagdashboard->column_count = 2;
}

// If error saving, register error and return
if (!$tagdashboard->save()) {
	register_error(elgg_echo('tagdashboards:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('tagdashboards-save-form');

// If we have a new tagdashboard, add river entry
if (!$guid) {
	// Add to river
	elgg_create_river_item(array(
		'view' => 'river/object/tagdashboard/create',
		'action_type' => 'create',
		'subject_guid' => elgg_get_logged_in_user_guid(),
		'object_guid' => $tagdashboard->guid
	));
}

// Forward on
system_message(elgg_echo('tagdashboards:success:save'));
forward($tagdashboard->getURL());
