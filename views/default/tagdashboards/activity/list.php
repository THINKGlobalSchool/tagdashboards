<?php
/**
 * Tag Dashboards Activity List View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 */

elgg_set_viewtype('spiffy');

$dashboard_guid = get_input('dashboard_guid');

$tagdashboard = get_entity($dashboard_guid);

$subtypes = unserialize($tagdashboard->subtypes);

$search = $tagdashboard->search;

// Owner guids
$owner_guids = $tagdashboard->owner_guids;

// Container guid
if ($tagdashboard->group_content) {
	$container_guid = $tagdashboard->container_guid;	
} else {
	$container_guid = ELGG_ENTITIES_ANY_VALUE;
}

// Dates
$lower_date = $tagdashboard->lower_date;
$upper_date = $tagdashboard->upper_date;

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = tagdashboards_get_enabled_subtypes();
}

$search = urldecode($search);

// Support for matching on multiple tags
if (is_array($tags = string_to_tag_array($search)) && count($tags) > 1) {
	$multi_tags = true;
	$search = $tags;
} else {
	$multi_tags = false;
}

$entities = array();
$batch_entities = array();

// Loop over subtypes and build entity array
foreach ($subtypes as $subtype) {		
	$entity_params = array(
		'created_time_upper' => $upper_date,
		'created_time_lower' => $lower_date,
		'owner_guids' => $owner_guids,
		'container_guid' => $container_guid,
		'types' => array('object'),
		'subtypes' => array($subtype),
		'limit' => 9999,
		'context' => $tagdashboard->context
	);

	// Filter tags
	if ($multi_tags) {
 		foreach($search as $tag) {
			$entity_params['metadata_name_value_pairs'][] = array(	
				'name' => 'tags', 
				'value' => $tag, 
				'operand' => '=',
				'case_sensitive' => FALSE
			);
		}
	} else { // Just one
		$entity_params['metadata_name_value_pairs'] = array(array(	
			'name' => 'tags', 
			'value' => $search, 
			'operand' => '=',
			'case_sensitive' => FALSE
		));
	}
	

	if ($subtype == 'image') {
		unset($entity_params['metadata_name_value_pairs']);
		$entity_params['tag'] = $search;
		$rows = am_get_entities_from_tag_and_container_tag($entity_params);
		foreach ($rows as $row) {
			$entities[] = $row->guid;
		}
	} else {
		$entity_params['callback'] = 'tagdashboards_row_to_guid';
		$entities = array_merge(elgg_get_entities_from_metadata($entity_params), $entities);
	}
}

if ($entities) {
	$options = array(
		'guids' => $entities,
		'limit' => 20,
		//'action_type' => 'create',
		'offset' => get_input('offset', 0)
	);

	$activity = elgg_list_entities($options);
}

echo $activity;