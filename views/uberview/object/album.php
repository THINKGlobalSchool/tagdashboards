<?php
/**
 * Tag Dashboards custom album object view
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */
// Get album's images
$images = elgg_get_entities(array('types' => 'object', 'subtypes' => 'image', 'container_guids' => $vars['entity']->getGUID(), 'limit' => 999));

if (is_array($images)) {	
	// display the simple image views. Uses 'object/image' view
	$list_entities = elgg_list_entities(array('types' => 'object', 'subtypes' => 'image', 
						'container_guids' => $vars['entity']->getGUID(), 'limit' => 34, 'full_view' => false, 'pagination' => false));	
	echo $list_entities;
}
