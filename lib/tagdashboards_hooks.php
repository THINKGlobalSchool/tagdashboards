<?php
/**
 * Tag Dashboards hooks
 *  - This lib will contain hooks for customizing the display of subtypes
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/* 
	Hook to change how photos are retrieved on the timeline
*/
function tagdashboards_timeline_photo_override_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		$params['params']['tagdashboards_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['limit'] = 0;
		$params['params']['offset'] = 0;
		$params['params']['types'] = array('object');
		$params['params']['subtypes'] = array('image');
		//$params['params']['callback'] = "entity_row_to_elggstar";
		
		$rows = tagdashboards_get_entities_from_tag_and_container_tag($params['params']);
		return tagdashboards_get_limited_entities_from_rows($rows);
	}
	return false;
}

/* 
	Override how photo's are listed to display both 
	photos and photos in albums with searched tag
	Uses: tagdashboards_get_images_and_albums_with_tag()
*/
function tagdashboards_photo_override_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		$params['params']['tagdashboards_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['callback'] = "entity_row_to_elggstar";
		return elgg_list_entities($params['params'], 'tagdashboards_get_entities_from_tag_and_container_tag');
	}
	return false;
}

/* Handler to register a timeline icon for blogs */
function tagdashboards_timeline_blog_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'blog') {
		return elgg_get_site_url() . "mod/tagdashboards/images/blog.gif";
	}
	return false;
}

/* Handler to register a timeline icon for images */
function tagdashboards_timeline_image_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		return elgg_get_site_url() . "mod/tagdashboards/images/image.gif";
	}
	return false;
}

/* Handler to register a timeline icon for tag dashboards */
function tagdashboards_timeline_tagdashboard_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'tagdashboard') {
		return elgg_get_site_url() . "mod/tagdashboards/images/tagdashboard.gif";
	}
	return false;
}

/* Handler to change name of Albums to Photos */
function tagdashboards_subtype_album_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'album') {
		return 'Photos';
	}
}



/* Example for exceptions */
function tagdashboards_exception_example($hook, $type, $returnvalue, $params) {
	// Unset a type (includes it in the list)
	unset($returnvalue[array_search('plugin', $returnvalue)]);
	
	// Add a new exception
	$returnvalue[] = 'todo';
	return $returnvalue;
}

/* Example for subtypes */
function tagdashboards_subtype_example($hook, $type, $returnvalue, $params) {
	// return custom content
	return "Test";
}
