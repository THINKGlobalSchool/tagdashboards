<?php
/**
 * Tag Dashboards edit action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$tagdashboard_guid = get_input('tagdashboard_guid');
$search = get_input('tagdashboard_search');
$title = get_input('tagdashboard_title');
$description = get_input('tagdashboard_description');
$tags = string_to_tag_array(get_input('tagdashboard_tags'));
$access = get_input('tagdashboard_access');
$subtypes = get_input('subtypes_enabled');
$groupby = get_input('tagdashboard_groupby');	// How are we grouping the content
$custom_tags = string_to_tag_array(get_input('tagdashboards_custom')); // Custom fields
$owner_guids = get_input('tagdashboard_owner_guids');
$lower_date = strtotime(get_input('tagdashboard_date_range_from', null));
$upper_date = strtotime(get_input('tagdashboard_date_range_to', null));

// Sticky form
elgg_make_sticky_form('tagdashboards-save-form');
if (!$title || !$search) {
	register_error(elgg_echo('tagdashboards:error:requiredfields'));
	forward(elgg_get_site_url() . 'pg/tagdashboards/edit/' . $tagdashboard_guid);
}


$tagdashboard = get_entity($tagdashboard_guid);
$tagdashboard->search = $search;
$tagdashboard->title = $title;
$tagdashboard->description = $description;
$tagdashboard->tags = $tags;
$tagdashboard->access_id = $access;
$tagdashboard->subtypes = serialize($subtypes);
$tagdashboard->groupby = $groupby;
$tagdashboard->custom_tags = $custom_tags;
$tagdashboard->owner_guids = $owner_guids;

// Set dates if provided
if ($lower_date) {
	$tagdashboard->lower_date = $lower_date;
}
if ($upper_date) {
	$tagdashboard->upper_date = $upper_date;
}

// If error saving, register error and return
if (!$tagdashboard->save()) {
	register_error(elgg_echo('tagdashboards:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('tagdashboards-save-form');

// Forward on
system_message(elgg_echo('tagdashboards:success:save'));
forward('pg/tagdashboards/view/' . $tagdashboard_guid);
