<?php
/**
 * Ubertags helper functions
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/* Build content for the ubertag search page */
function ubertags_get_page_content_search() {
	$content_info['title'] = elgg_echo('ubertags:title:search');
	$content_info['layout'] = 'one_column';
	$content = elgg_view('ubertags/ubertags_search_container');
	$content_info['content'] = elgg_view_title($content_info['title']) . $content;
	return $content_info;
}

function ubertags_get_page_content_admin_settings() {
	$content_info['title'] = elgg_echo('ubertags:title:adminsettings');
	$content_info['layout'] = 'administration';
	$content = elgg_view('ubertags/admin/settings');	
	$content_info['content'] = elgg_view_title($content_info['title']) . $content;
	return $content_info;
}

/* Helper function tog grab the plugins enabled subtypes */
function ubertags_get_enabled_subtypes() {
	return unserialize(get_plugin_setting('enabled_subtypes', 'ubertags'));
}

/* Get all registered subtypes (for admins) */
function ubertags_get_site_subtypes() {
	
	// Some exceptions, don't really want these in the list
	$exceptions = array(
		'plugin', 
		'widget', 
		'sitepages_page', 
		'page_top', 
		'test_subtype',
		'site'
	);
	
	// Query to grab subtypes
	$query = "SELECT subtype FROM elgg_entity_subtypes WHERE type = 'object';";
	
	// Execute
	$subtypes = get_data($query, 'ubertags_get_site_subtype_callback');
	
	// Filter exceptions	
	$filtered_subtypes = array_diff($subtypes, $exceptions);
	
	return $filtered_subtypes;
}

/**
 *  Callback to handle results from the ubertags_get_subtypes() query 
 *  turns the result from a stdClass to a string
 */
function ubertags_get_site_subtype_callback($data) {
	return $data->subtype;
}

?>