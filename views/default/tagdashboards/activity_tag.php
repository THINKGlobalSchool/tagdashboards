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

$activities = tagdashboards_get_group_activities();
$search = $vars['search'];
$subtypes = $vars['subtypes'];

foreach($activities as $activity) {
	$content .= elgg_view('tagdashboards/activity_tag_container', array(
		'search' => $search, 
		'activity' => $activity, 
		'subtypes' => $subtypes,
	));
}

echo $content . "<div style='clear: both;'></div>";