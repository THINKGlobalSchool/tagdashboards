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

$activities = tagdashboards_get_group_activities();
$group_guid = $vars['container_guid'];
foreach($activities as $activity) {
	$content .= elgg_view('tagdashboards/activity_container', array(
		'container_guid' => $group_guid, 
		'activity' => $activity, 
	));
}

echo $content . "<div style='clear: both;'></div>";