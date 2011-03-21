<?php
/**
 * Ubertags admin enable entities action
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
gatekeeper();

// Get inputs
$title = get_input('ubertag_title');
$description = get_input('ubertag_description');
$tags = string_to_tag_array(get_input('ubertag_tags'));
$access = get_input('ubertag_access');
$search = get_input('ubertag_search');
$subtypes = get_input('subtypes_enabled');
$groupby = get_input('ubertag_groupby');	// How are we grouping the content
$custom_tags = string_to_tag_array(get_input('ubertags_custom')); // Custom fields

// Sticky form
elgg_make_sticky_form('ubertags_save_form');
if (!$title) {
	register_error(elgg_echo('ubertags:error:requiredfields'));
	forward(elgg_get_site_url() . 'pg/ubertags/search#' . $search);
}

$ubertag = new ElggObject();
$ubertag->subtype = 'ubertag';
$ubertag->title = $title;
$ubertag->description = $description;
$ubertag->tags = $tags;
$ubertag->access_id = $access;
$ubertag->search = $search;
$ubertag->subtypes = serialize($subtypes);
$ubertag->groupby = $groupby;
$ubertag->custom_tags = $custom_tags;

// If error saving, register error and return
if (!$ubertag->save()) {
	register_error(elgg_echo('ubertags:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('ubertags_save_form');

// Add to river
add_to_river('river/object/ubertag/create', 'create', get_loggedin_userid(), $ubertag->getGUID());

// Forward on
system_message(elgg_echo('ubertags:success:save'));
forward('pg/ubertags');
