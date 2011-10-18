<?php
/**
 * Tag Dashboards save action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
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
$owner_guids	 	= get_input('owner_guids');
$container_guid 	= get_input('container_guid', NULL);
$lower_date 		= strtotime(get_input('lower_date', null));
$upper_date 		= strtotime(get_input('upper_date', null));
$column_count		= get_input('columns');

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
$tagdashboard->owner_guids = $owner_guids;
$tagdashboard->lower_date = $lower_date;
$tagdashboard->upper_date = $upper_date;

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
	add_to_river('river/object/tagdashboard/create', 'create', get_loggedin_userid(), $tagdashboard->getGUID());
}

// Forward on
system_message(elgg_echo('tagdashboards:success:save'));
forward($tagdashboard->getURL());
