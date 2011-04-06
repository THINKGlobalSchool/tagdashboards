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
	$activity_name = $activity['name'];
	$activity_tag = $activity['tag'];

	// Build container
	$content .= elgg_view('tagdashboards/content/container', array(
		'heading' => $activity_name,
		'container_class' => 'tagdashboards-activity',
		'id' => $activity_tag,
	));
	
	// Build JS
	$content .= <<<HTML
	<script type='text/javascript'>
		$(document).ready(function() {
			elgg.tagdashboards.load_tagdashboards_activity_content("$activity_tag", "$group_guid", null);
		});
	</script>
HTML;
}

echo $content . "<div style='clear: both;'></div>";