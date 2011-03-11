<?php
/**
 * Ubertags subtype content
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
set_input('search_viewtype', 'list');

// If this entity doesn't have a custom uberview, use default
if (!elgg_view_exists("object/{$vars['subtype']}")) {
	elgg_set_viewtype('default');
}


// Set the pager js (which function to use when reloading pagination)
$page_js = "elgg.ubertags.load_ubertags_subtype_content(\"{$vars['subtype']}\", \"{$vars['search']}\", \"%s\");";

set_input('page_js', $page_js);

/* 
	Setting up a pile of default params. metadata_name_value_pairs
	is what makes the tag magic happen. This might even work 
	for multiple tags. (2 Minutes later..) No it doesn't but 
	that'd be cool.
*/
$params = array(
	'types' => array('object'),
	'subtypes' => array($vars['subtype']),
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 10,
	'offset' => $vars['offset'] ? $vars['offset'] : 0,
	'full_view' => FALSE,
	'listtypetoggle' => FALSE,
	'listtype' => 'list',
	'pagination' => TRUE,
	'metadata_name_value_pairs' => array(	'name' => 'tags', 
											'value' => rawurldecode($vars['search']), 
											'operand' => '=',
											'case_sensitive' => FALSE)
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
