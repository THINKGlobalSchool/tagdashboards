<?php
/**
 * Ubertag timeline results endpoint
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * INFO: http://simile.mit.edu/wiki/How_to_Create_Event_Source_Files#JSON_files
 */

$ubertag = get_entity($vars['guid']);

// Type determines how detailed the results will be, either 'overview' or 'detailed'
$type = $vars['type'];

$subtypes = unserialize($ubertag->subtypes);

$search = $ubertag->search;

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = ubertags_get_enabled_subtypes();
}

// Params
$params = array(
	'types' => array('object'),
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 0,						
);

$params['metadata_name_value_pairs'] = array(array(
	'name' => 'tags', 
	'value' => $search, 
	'operand' => '=',
	'case_sensitive' => FALSE
));

// Need to use wheres here, because 'time_created' isn't metadata..
if ($vars['min']) {
	$params['wheres'][] = "e.time_created > {$vars['min']}";	
}

if ($vars['max']) {
	$params['wheres'][] = "e.time_created < {$vars['max']}";
}

$json = array();

$json['wiki-url'] = '';
$json['wiki-section'] = 'Ubertags Timeline';
$json['dateTimeFormat'] = 'Gregorian';
$json['events'] = array();

$entities = array();

foreach ($subtypes as $subtype) {
	$params['subtypes'] = array($subtype);
	
	if(!$return = trigger_plugin_hook('ubertags:timeline:subtype', $subtype, array('search' => $search, 'params' => $params))) {
		$return = elgg_get_entities_from_metadata($params);
	}

	$entities[$subtype] = $return;
	
	
	if ($entities[$subtype]) {
		foreach ($entities[$subtype] as $entity) {
			$json['events'][] = ubertags_entity_to_timeline_event_array($entity, $type);
		}
	}
	$count++;
}
echo json_encode($json);
