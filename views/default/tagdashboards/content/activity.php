<?php
/**
 * Tag Dashboards activity content
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Set custom viewtype
elgg_set_viewtype('uberview');
set_input('search_viewtype', 'list');

$subtypes = tagdashboards_get_enabled_subtypes();

// Set the pager js (which function to use when reloading pagination)
$page_js = "elgg.tagdashboards.load_tagdashboards_activity_content(\"{$vars['activity']}\", \"{$vars['container_guid']}\", \"%s\");";

set_input('page_js', $page_js);

// Params
$params = array(
	'types' => array('object'),
	'subtypes' => tagdashboards_get_enabled_subtypes(),
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
	echo elgg_view('tagdashboards/noresults', array(), false, false, 'default');
}

