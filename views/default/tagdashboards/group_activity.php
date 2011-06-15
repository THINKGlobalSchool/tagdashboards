<?php
/**
 * Tag Dashboards group by activity content container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$activities = tagdashboards_get_activities();
$group_guid = $vars['container_guid'];

// Loop over each activity
foreach($activities as $activity) {

	$params = array(
		'container_guid' => $group_guid,
		'types' => array('object'),
		'limit' => 10,
		'title' => $activity['name'],
		'listing_type' => 'simpleicon',
		'restrict_tag' => TRUE,
		'module_type' => 'featured',
		'module_id' => $activity['tag'],
		'module_class' => 'tagdashboards-container',
		'tags' => array($search, $activity['tag']),
	);
	
	// Default module
	$content = elgg_view('modules/ajaxmodule', $params);
	
	echo $content;
}
// Don't need to call the dashboard init here, because we're not ajax loading this view