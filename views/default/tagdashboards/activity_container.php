<?php
/**
 * Tag Dashboards activity container
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$activity = $vars['activity'];

// Div, JS label
$dashboard_id = $activity['tag'] . '_content';


$spinner = elgg_view('tagdashboards/ajax_spinner', array('id' => 'loading_' . $activity['tag']));

?>
<div class='tagdashboards-container tagdashboards-activity'>
	<h3 class='tagdashboards-container-title'><?php echo $activity['name']; ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $dashboard_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		elgg.tagdashboards.load_tagdashboards_activity_content("<?php echo $activity['tag']; ?>", "<?php echo $vars['container_guid']; ?>", null);
	});
</script>