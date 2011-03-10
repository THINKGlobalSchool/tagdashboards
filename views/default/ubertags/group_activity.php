<?php
/**
 * Ubertags group by activity content container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$activities = ubertags_get_group_activities();
$group_guid = $vars['container_guid'];
foreach($activities as $activity) {
	$content .= elgg_view('ubertags/activity_container', array(
		'container_guid' => $group_guid, 
		'activity' => $activity, 
	));
}

echo $content . "<div style='clear: both;'></div>";