<?php
/**
 * Tag Dashboards subtypes content container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Grab subtypes if they were supplied
$subtypes = $vars['subtypes'];

// Get search
$search = $vars['search'];

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = tagdashboards_get_enabled_subtypes();
}

// Loop over and display each
foreach ($subtypes as $subtype) {
	// Check if anyone wants to change the heading for their subtype
	$subtype_heading = trigger_plugin_hook('tagdashboards:subtype:heading', $subtype, array(), false);
	if (!$subtype_heading) {
		// Use default item:object:subtype as this is usually defined 
		$subtype_heading = elgg_echo('item:object:' . $subtype);
	}
	
	// Build container
	$content .= elgg_view('tagdashboards/content/container', array(
		'heading' => $subtype_heading,
		'container_class' => 'tagdashboards-subtype',
		'id' => $subtype,
	));
	
	// Build JS
	$content .= <<<HTML
	<script type='text/javascript'>
		$(document).ready(function() {
			elgg.tagdashboards.load_tagdashboards_subtype_content("$subtype", "$search", null);
		});
	</script>
HTML;
}

echo $content . "<div style='clear: both;'></div>";
