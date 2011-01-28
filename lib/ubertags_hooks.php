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

/* Override default event converstion for blog entities */
function ubertags_timeline_blog_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'blog') {
		// These first three are required
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $params['entity']->time_created)));; // full date format
		$event['isDuration'] = FALSE;
		$event['title'] = $params['entity']->title; 
		$event['description'] = elgg_get_excerpt($params['entity']->description);
		$event['icon'] = elgg_get_site_url() . "mod/ubertags/images/blog.gif";
		$event['link'] = $params['entity']->getURL();
		return $event;
	} 
}

/* Override default event converstion for image entities */
function ubertags_timeline_image_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'image') {
		// These first three are required
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $params['entity']->time_created))); // full date format
		$event['isDuration'] = FALSE;
		$event['title'] = $params['entity']->title; 
		$event['description'] = elgg_get_excerpt($params['entity']->description);
		$event['image'] = elgg_get_site_url() . "pg/photos/thumbnail/{$params['entity']->getGUID()}/small";
		$event['icon'] = elgg_get_site_url() . "mod/ubertags/images/image.gif";
		$event['link'] = $params['entity']->getURL();
		return $event;
	} 
}

/* Override default event converstion for image entities */
function ubertags_timeline_ubertag_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'ubertag') {
		// These first three are required
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $params['entity']->time_created))); // full date format
		$event['isDuration'] = FALSE;
		$event['title'] = $params['entity']->title; 
		$event['description'] = elgg_get_excerpt($params['entity']->description);
		$event['icon'] = elgg_get_site_url() . "mod/ubertags/images/ubertag.gif";
		$event['link'] = $params['entity']->getURL();
		return $event;
	} 
}

/* Override default event converstion for image entities */
function ubertags_timeline_simplekaltura_handler($hook, $type, $returnvalue, $params) {
	if ($type == 'simplekaltura_video') {
		// These first three are required
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $params['entity']->time_created))); // full date format
		$event['isDuration'] = FALSE;
		$event['title'] = $params['entity']->title; 
		$event['description'] = elgg_get_excerpt($params['entity']->description);
		$event['icon'] = elgg_get_site_url() . "mod/ubertags/images/simplekaltura_video.gif";
		$event['link'] = $params['entity']->getURL();
		return $event;
	} 
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