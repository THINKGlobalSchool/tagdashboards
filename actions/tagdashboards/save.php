<?php
/**
 * Tag Dashboards admin enable entities action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get inputs
$title = get_input('tagdashboard_title');
$description = get_input('tagdashboard_description');
$tags = string_to_tag_array(get_input('tagdashboard_tags'));
$access = get_input('tagdashboard_access');
$search = get_input('tagdashboard_search');
$subtypes = get_input('subtypes_enabled');
$groupby = get_input('tagdashboard_groupby');	// How are we grouping the content
$custom_tags = string_to_tag_array(get_input('tagdashboards_custom')); // Custom fields
$owner_guids = get_input('tagdashboard_owner_guids');

// Sticky form
elgg_make_sticky_form('tagdashboards-save-form');
if (!$title) {
	register_error(elgg_echo('tagdashboards:error:requiredfields'));
	forward(elgg_get_site_url() . 'pg/tagdashboards/search#' . $search);
}

$tagdashboard = new ElggObject();
$tagdashboard->subtype = 'tagdashboard';
$tagdashboard->title = $title;
$tagdashboard->description = $description;
$tagdashboard->tags = $tags;
$tagdashboard->access_id = $access;
$tagdashboard->search = $search;
$tagdashboard->subtypes = serialize($subtypes);
$tagdashboard->groupby = $groupby;
$tagdashboard->custom_tags = $custom_tags;
$tagdashboard->owner_guids = $owner_guids;

// If error saving, register error and return
if (!$tagdashboard->save()) {
	register_error(elgg_echo('tagdashboards:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('tagdashboards-save-form');

// Add to river
add_to_river('river/object/tagdashboard/create', 'create', get_loggedin_userid(), $tagdashboard->getGUID());

// Forward on
system_message(elgg_echo('tagdashboards:success:save'));
forward('pg/tagdashboards');
