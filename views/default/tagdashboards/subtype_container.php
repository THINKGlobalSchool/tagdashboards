<?php
/**
 * Tag Dashboards subtype container
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Div, JS label
$dashboard_id = $vars['subtype'] . '_content';

// Check if anyone wants to change the heading for their subtype
$subtype_heading = trigger_plugin_hook('tagdashboards:subtype:heading', $vars['subtype'], array(), false);
if (!$subtype_heading) {
	// Use default item:object:subtype as this is usually defined 
	$subtype_heading = elgg_echo('item:object:' . $vars['subtype']);
}

$spinner = elgg_view('tagdashboards/ajax_spinner', array('id' => 'loading_' . $vars['subtype']));

?>
<div class='tagdashboards-container tagdashboards-subtype'>
	<h3 class='tagdashboards-container-title'><?php echo $subtype_heading; ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $dashboard_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		elgg.tagdashboards.load_tagdashboards_subtype_content("<?php echo $vars['subtype']; ?>", "<?php echo $vars['search']; ?>", null);
	});
</script>