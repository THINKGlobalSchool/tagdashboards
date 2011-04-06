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

$activities = tagdashboards_get_activities();
$search = $vars['search'];
$subtypes = $vars['subtypes'];
$json_subtypes = json_encode($subtypes);

$content = <<<HTML
	<script type='text/javascript'>
		var subtypes = $.parseJSON('$json_subtypes');
	</script>
HTML;

// Loop over each activity and build content
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
			elgg.tagdashboards.load_tagdashboards_activity_tag_content("$activity_tag", "$search", subtypes, null);
		});
	</script>
HTML;
}

echo $content . "<div style='clear: both;'></div>";