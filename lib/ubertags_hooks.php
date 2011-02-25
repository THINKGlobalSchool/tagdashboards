<?php
/**
 * Ubertags hooks
 *  - This lib will contain hooks for customizing the display of subtypes
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/* 
	Hook to change how photos are retrieved on the timeline
*/
function ubertags_timeline_photo_override_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		$params['params']['ubertags_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['limit'] = 0;
		$params['params']['offset'] = 0;
		$params['params']['types'] = array('object');
		$params['params']['subtypes'] = array('image');
		//$params['params']['callback'] = "entity_row_to_elggstar";
		
		$rows = ubertags_get_entities_from_tag_and_container_tag($params['params']);
		return ubertags_get_limited_entities_from_rows($rows);
	}
	return false;
}

/* 
	Override how photo's are listed to display both 
	photos and photos in albums with searched tag
	Uses: ubertags_get_images_and_albums_with_tag()
*/
function ubertags_photo_override_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		$params['params']['ubertags_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['callback'] = "entity_row_to_elggstar";
		return elgg_list_entities($params['params'], 'ubertags_get_entities_from_tag_and_container_tag');
	}
	return false;
}

/* Handler to register a timeline icon for blogs */
function ubertags_timeline_blog_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'blog') {
		return elgg_get_site_url() . "mod/ubertags/images/blog.gif";
	}
	return false;
}

/* Handler to register a timeline icon for images */
function ubertags_timeline_image_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		return elgg_get_site_url() . "mod/ubertags/images/image.gif";
	}
	return false;
}

/* Handler to register a timeline icon for ubertags */
function ubertags_timeline_ubertag_icon_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'ubertag') {
		return elgg_get_site_url() . "mod/ubertags/images/ubertag.gif";
	}
	return false;
}

/* Handler to change name of Albums to Photos */
function ubertags_subtype_album_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'album') {
		return 'Photos';
	}
}



/* Example for exceptions */
function ubertags_exception_example($hook, $type, $returnvalue, $params) {
	// Unset a type (includes it in the list)
	unset($returnvalue[array_search('plugin', $returnvalue)]);
	
	// Add a new exception
	$returnvalue[] = 'todo';
	return $returnvalue;
}

/* Example for subtypes */
function ubertags_subtype_example($hook, $type, $returnvalue, $params) {
	// return custom content
	return "Test";
}
?>