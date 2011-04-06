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

$custom = $vars['custom_tags'];
$search = $vars['search'];
$subtypes = $vars['subtypes'];
$json_subtypes = json_encode($subtypes);
$json_owner_guids = json_encode($owner_guids);
$owner_guids = $vars['owner_guids'];

$content = <<<HTML
	<script type='text/javascript'>
		var subtypes = $.parseJSON('$json_subtypes');
		var owner_guids = $.parseJSON('$json_owner_guids');
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
			elgg.tagdashboards.load_tagdashboards_custom_content("$tag", "$search", subtypes, owner_guids, null);
		});
	</script>
HTML;
}

echo $content . "<div style='clear: both;'></div>";
