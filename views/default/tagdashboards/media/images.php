<?php
/**
 * Tag Dashboards Images Media View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['dashboard_guid']
 */

$dashboard_guid = elgg_extract('dashboard_guid', $vars);

$dashboard = get_entity($dashboard_guid);

$entity_options = tagdashboards_get_media_entities_options($dashboard_guid, array('subtypes' => array('image')));

unset($entity_options['metadata_name_value_pairs']);

$search = rawurldecode($dashboard->search);

// Support for matching on multiple tags
if (is_array($tags = string_to_tag_array($search)) && count($tags) > 1) {
	$param = 'tags';
	$search = $tags;
} else {
	$param = 'tag';
}

$entity_options[$param] = $search;

// Ajaxmodule options
$module_options = array(
	'title' => 'Photos',
	'albums_images'=> TRUE,
	'module_type' => 'info',
	'module_id' => $type,
	'module_class' => 'tagdashboards-media-images-container',
);

$options = array_merge($entity_options, $module_options);
$options['limit'] = 999; // @todo limit 0 is showing pagination for some reason..

// Default module
echo elgg_view('modules/ajaxmodule', $options);
