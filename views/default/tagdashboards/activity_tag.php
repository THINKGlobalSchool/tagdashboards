<?php
/**
 * Tag Dashboards group by activity, based on search tag container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get activities
$activities = tagdashboards_get_activities();
$search = $vars['search'];

// Get subtypes
$subtypes = $vars['subtypes'];

// Get ownerguids
$owner_guids = $vars['owner_guids'];

// Dates
$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

$search = rawurldecode($search);

// Loop over each activity and build content
foreach($activities as $activity) {
	$params = array(
		'created_time_upper' => $upper_date,
		'created_time_lower' => $lower_date,
		'owner_guids' => $owner_guids,
		'types' => array('object'),
		'subtypes' => $subtypes,
		'limit' => 10,
		'title' => $activity['name'],
		'listing_type' => 'simpleicon',
		'restrict_tag' => TRUE,
		'module_type' => 'featured',
		'module_id' => $activity['tag'],
		'module_class' => 'tagdashboard-module',
		//'tags' => array($search, $activity['tag']),
	);

	// Support for matching on multiple tags
	if (is_array($multi_tags = string_to_tag_array($search))) {
		array_push($multi_tags, $activity['tag']);
		$params['tags'] = $multi_tags;
	} else {
		$params['tags'] = array($search, $activity['tag']);
	}
	
	// Default module
	$content = elgg_view('modules/ajaxmodule', $params);
	
	echo $content;
}

echo "<script>elgg.modules.ajaxmodule.init();</script><div style='clear: both;'></div>";