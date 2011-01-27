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

$subtypes = unserialize($ubertag->subtypes);

$search = $ubertag->search;


// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = ubertags_get_enabled_subtypes();
}

// Params
$params = array(
	'type' => 'object',
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 0,
	'metadata_name_value_pairs' => array(	'name' => 'tags', 
											'value' => $ubertag->search, 
											'operand' => '=',
											'case_sensitive' => FALSE)
);


$json = array();

$json['wiki-url'] = '';
$json['wiki-section'] = 'Ubertags Timeline';
$json['dateTimeFormat'] = 'Gregorian';
$json['events'] = array();

$entities = array();

foreach ($subtypes as $subtype) {
	$params['subtype'] = $subtype;
	$entities[$subtype] = elgg_get_entities_from_metadata($params);
	
	foreach ($entities[$subtype] as $entity) {
		$json['events'][] = ubertags_entity_to_timeline_event_array($entity);
	}
	
	$count++;
}

echo json_encode($json);

/*$items = array(
	'wiki-url' => 'http://simile.mit.edu/shelf/', 
	'wiki-section' => 'Ubertags Timeline',
	'dateTimeFormat' => 'Gregorian',
	'events' => array(
		array(
			'start' => 'Sun January 23 2011 00:00:00 GMT-0600',
			'title' => 'Stuff',
			'durationEvent' => FALSE,
		),
		array(
			'start' => 'Mon January 24 2011 00:00:00 GMT-0600',
			'title' => 'More Stuff',
			'durationEvent' => FALSE,
		),
		array(
			'start' => 'Sat January 22 2011 00:00:00 GMT-0600',
			'title' => 'Less Stuff',
			'durationEvent' => FALSE,
		),
	)
);*/

//echo json_encode($items);

?>