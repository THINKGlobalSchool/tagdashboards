<?php
/**
 * Ubertags edit action
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
$ubertag_guid = get_input('ubertag_guid');
$search = get_input('ubertag_search');
$title = get_input('ubertag_title');
$description = get_input('ubertag_description');
$tags = string_to_tag_array(get_input('ubertag_tags'));
$access = get_input('ubertag_access');
$subtypes = get_input('subtypes_enabled');

// Sticky form
elgg_make_sticky_form('ubertags_save_form');
if (!$title || !$search) {
	register_error(elgg_echo('ubertags:error:requiredfields'));
	forward(elgg_get_site_url() . 'pg/ubertags/edit/' . $ubertag_guid);
}


$ubertag = get_entity($ubertag_guid);
$ubertag->search = $search;
$ubertag->title = $title;
$ubertag->description = $description;
$ubertag->tags = $tags;
$ubertag->access_id = $access;
$ubertag->subtypes = serialize($subtypes);

// If error saving, register error and return
if (!$ubertag->save()) {
	register_error(elgg_echo('ubertags:error:save'));
	forward(REFERER);
}

// Clear sticky form
elgg_clear_sticky_form('ubertags_save_form');

// Forward on
system_message(elgg_echo('ubertags:success:save'));
forward('pg/ubertags/view/' . $ubertag_guid);


?>