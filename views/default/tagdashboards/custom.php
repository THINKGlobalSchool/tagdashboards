<?php
/**
 * Tag Dashboards group by user defined, based on search tag container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Custom tags
$custom = $vars['custom_tags'];
$search = $vars['search'];

// Get Subtypes
$subtypes = $vars['subtypes'];
$json_subtypes = json_encode($subtypes);

// Get ownerguids
$owner_guids = $vars['owner_guids'];
$json_owner_guids = json_encode($owner_guids);

// Dates
$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

$content = <<<HTML
	<script type='text/javascript'>
		var subtypes = $.parseJSON('$json_subtypes');
		var owner_guids = $.parseJSON('$json_owner_guids');
		var lower_date = '$lower_date';
		var upper_date = '$upper_date';
	</script>
HTML;

foreach($custom as $tag) {
	// Build container
	$content .= elgg_view('tagdashboards/content/container', array(
		'heading' => ucfirst($tag),
		'container_class' => 'tagdashboards-custom',
		'id' => $tag,
	));

	// Build JS
	$content .= <<<HTML
	<script type='text/javascript'>
		$(document).ready(function() {
			elgg.tagdashboards.load_tagdashboards_custom_content("$tag", "$search", subtypes, owner_guids, lower_date, upper_date, null);
		});
	</script>
HTML;
}

echo $content . "<div style='clear: both;'></div>";
