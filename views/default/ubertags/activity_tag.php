<?php
/**
 * Ubertags group by activity, based on search tag container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$activities = ubertags_get_group_activities();
$search = $vars['search'];
$subtypes = $vars['subtypes'];

$content .= "<div class='ubertag-big-title'>
				$search
			</div>";

foreach($activities as $activity) {
	$content .= elgg_view('ubertags/activity_tag_container', array(
		'search' => $search, 
		'activity' => $activity, 
		'subtypes' => $subtypes,
	));
}

echo $content . "<div style='clear: both;'></div>";