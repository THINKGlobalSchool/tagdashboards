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
	Override how photo's are grabbed to display both 
	photos and photos in albums with searched tag
*/
function ubertags_photo_override_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		// store and get rid of offset for now
		$offset = $params['params']['offset'];
		unset($params['params']['offset']);
		
		// Set limit to 0 
		$params['params']['limit'] = 0;
		
		// Count images first
		$params['params']['count'] = TRUE;
		$count = elgg_get_entities_from_metadata($params['params']);
		
		// Get all images
		$params['params']['count'] = FALSE;
		
		if (!$images = elgg_get_entities_from_metadata($params['params'])) {
			$images = array();
		}
		
		// Get albums matching search
		$params['params']['subtype'] = 'album';
		$albums = elgg_get_entities_from_metadata($params['params']);
		
		// Loop and grab each image within the albums
		foreach ($albums as $album) {
			$album_images = elgg_get_entities(array(
				'types' => 'object', 
				'subtypes' => 'image', 
				'container_guids' => $album->getGUID(), 
				'limit' => 0
			));
			
			if ($album_images) {
				$images = array_merge($images, $album_images);
			}
		}
		
		// Filter out dupes
		$images = array_filter($images, 'ubertags_image_unique');
		
		// Image limit a nice round 12
		$limit = 12;
	
		// List out entities
		$return = elgg_view_entity_list(
			array_slice($images, $offset, $limit), // Note to self, array_slice is awesome
			count($images), 
			$offset,
			$limit, 
			$params['params']['full_view'], 
			$params['params']['view_type_toggle'], 
			$params['params']['pagination']
		);
				
		return $return;
	}
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