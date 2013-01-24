<?php
/**
 * tagdashboard timeline results endpoint
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * INFO: http://simile.mit.edu/wiki/How_to_Create_Event_Source_Files#JSON_files
 */
$tagdashboard = get_entity($vars['guid']);

// Type determines how detailed the results will be, either 'overview' or 'detailed'
$type = $vars['type'];

$subtypes = unserialize($tagdashboard->subtypes);

$search = $tagdashboard->search;

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = tagdashboards_get_enabled_subtypes();
}

// Params
$params = array(
	'types' => array('object'),
	'limit' => 0,						
);

if ($tagdashboard->type == 'users') {
	$params['user_guids'] = $tagdashboard->user_guids;
	error_log('uo');
} else {
	$params['owner_guids'] = $tagdashboard->owner_guids;
}

// Support for multiple tags
$multi_tags = string_to_tag_array($search);
if (is_array($multi_tags) && count($multi_tags) > 1) {
	foreach ($multi_tags as $tag) {
		$params['metadata_name_value_pairs'][] = array(
			'name' => 'tags',
			'value' => $tag,
			'operand' => '=',
			'case_sensitive' => FALSE
		);
	}

	$search = $multi_tags;
} else {
	$params['metadata_name_value_pairs'] = array(array(
		'name' => 'tags', 
		'value' => $search, 
		'operand' => '=',
		'case_sensitive' => FALSE
	));
}

// Need to use wheres here, because 'time_created' isn't metadata..
if ($vars['min']) {
	$params['wheres'][] = "e.time_created > {$vars['min']}";	
}

if ($vars['max']) {
	$params['wheres'][] = "e.time_created < {$vars['max']}";
}

$json = array();

$json['wiki-url'] = '';
$json['wiki-section'] = 'Tag Dashboards Timeline';
$json['dateTimeFormat'] = 'Gregorian';
$json['events'] = array();

$entities = array();

foreach ($subtypes as $subtype) {
	$params['subtypes'] = array($subtype);
	
	if(!$return = elgg_trigger_plugin_hook('tagdashboards:timeline:subtype', $subtype, array('search' => $search, 'params' => $params))) {
		$return = elgg_get_entities_from_metadata($params);
	}

	$entities[$subtype] = $return;
	
	
	if ($entities[$subtype]) {
		foreach ($entities[$subtype] as $entity) {
			$json['events'][] = tagdashboards_entity_to_timeline_event_array($entity, $type);
		}
	}
	$count++;
}
echo json_encode($json);
