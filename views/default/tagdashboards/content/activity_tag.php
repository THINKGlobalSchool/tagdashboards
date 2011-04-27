<?php
/**
 * Tag Dashboards activity tag content
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

$subtypes = $vars['subtypes'];
$owner_guids = $vars['owner_guids'];

$owner_guids = $vars['owner_guids'];
// If we weren't supplied an array of owner guids, use default 
if (((int)$owner_guids == 0 || (!is_int((int)$owner_guids))) && !is_array($owner_guids)) {
	$owner_guids = ELGG_ENTITIES_ANY_VALUE;
}

$json_subtypes = json_encode($subtypes);
$json_owner_guids = json_encode($owner_guids);

$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

// Remove image related subtypes until I figure out what to do with them..
foreach($subtypes as $idx => $subtype) {
	if ($subtype == 'image' || $subtype == 'album') {
		unset($subtypes[$idx]);
	}
}

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = tagdashboards_get_enabled_subtypes();
}

// Set the pager js (which function to use when reloading pagination)
$page_js = "elgg.tagdashboards.load_tagdashboards_activity_tag_content(\"{$vars['activity']}\", \"{$vars['search']}\", $json_subtypes, $json_owner_guids, \"{$lower_date}\", \"{$upper_date}\", \"%s\");";

set_input('page_js', $page_js);

// Params
$params = array(
	'types' => array('object'),
	'subtypes' => $subtypes,
	'owner_guids' => $owner_guids,
	'limit' => 10,
	'offset' => $vars['offset'] ? $vars['offset'] : 0,
	'full_view' => FALSE,
	'listtypetoggle' => FALSE,
	'listtype' => 'list',
	'pagination' => TRUE,
	// Search where tag == activity AND tag == search
	'metadata_name_value_pairs' => array(array(	
											'name' => 'tags', 
											'value' => rawurldecode($vars['activity']), 
											'operand' => '=',
											'case_sensitive' => FALSE),
										array(	
											'name' => 'tags', 
											'value' => rawurldecode($vars['search']), 
											'operand' => '=',
											'case_sensitive' => FALSE)
										)
);

// If we were supplied with a lower date, include it 
if ((int)$lower_date) {
	$params['created_time_lower'] = $lower_date;
}

// If we were supplied with an upper date, include it
if ((int)$upper_date) {
	$params['created_time_upper'] = $upper_date;
}

$entity_list = elgg_list_entities($params, 'elgg_get_entities_from_metadata');

if (!empty($entity_list)) {
	echo $entity_list; 
} else {
	// Might be in uberview here, make sure to display default
	echo elgg_view('tagdashboards/noresults', array(), false, false, 'default');
}
