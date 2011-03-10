<?php
/**
 * Ubertags activity content
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

$subtypes = ubertags_get_enabled_subtypes();



// Params
$params = array(
	'types' => array('object'),
	'subtypes' => ubertags_get_enabled_subtypes(),
	'owner_guid' => ELGG_ENTITIES_ANY_VALUE,
	'container_guid' => $vars['container_guid'],
	'limit' => 10,
	'offset' => $vars['offset'] ? $vars['offset'] : 0,
	'full_view' => FALSE,
	'listtypetoggle' => FALSE,
	'listtype' => 'list',
	'pagination' => TRUE,
	'metadata_name_value_pairs' => array(	'name' => 'tags', 
											'value' => rawurldecode($vars['activity']), 
											'operand' => '=',
											'case_sensitive' => FALSE)
);


$entity_list = elgg_list_entities($params, 'elgg_get_entities_from_metadata');


if (!empty($entity_list)) {
	echo $entity_list; 
} else {
	// Might be in uberview here, make sure to display default
	echo elgg_view('ubertags/noresults', array(), false, false, 'default');
}
	


?>
