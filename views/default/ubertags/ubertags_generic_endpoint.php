<?php
/**
 * Ubertags generic endpoint
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Set custom viewtype
elgg_set_viewtype('uberview');

// If this entity doesn't have a custom uberview, use default
if (!elgg_view_exists("object/{$vars['subtype']}")) {
	elgg_set_viewtype('default');
}

/* 
	Setting up a pile of default params. metadata_name_value_pairs
	is what makes the tag magic happen. This might even work 
	for multiple tags. (2 Minutes later..) No it doesn't but 
	that'd be cool.
*/
$params = array(
	'type' => 'object',
	'subtype' => $vars['subtype'],
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 10,
	'offset' => $vars['offset'] ? $vars['offset'] : 0,
	'full_view' => FALSE,
	'view_type_toggle' => FALSE,
	'pagination' => TRUE,
	'metadata_name_value_pairs' => array(	'name' => 'tags', 
											'value' => $vars['search'], 
											'operands' => 'contains')
);

// See if anyone has registered a hook to display their subtype appropriately
if (!$entity_list = trigger_plugin_hook('ubertags:subtype', $vars['subtype'], array('search' => $vars['search'], 'params' => $params), false)) {
	$entity_list = elgg_list_entities($params, 'elgg_get_entities_from_metadata');
} 

if (!empty($entity_list)) {
	echo $entity_list; 
} else {
	// Might be in uberview here, make sure to display default
	echo elgg_view('ubertags/noresults', array(), false, false, 'default');
}
	


?>
