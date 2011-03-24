<?php
/**
 * Tag Dashboards custom container
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Div, JS label
$dashboard_id = $vars['group'] . '_content';

$subtypes = json_encode($vars['subtypes']);

$spinner = elgg_view('tagdashboards/ajax_spinner', array('id' => 'loading_' . $vars['group']));

?>
<div class='tagdashboards-container tagdashboards-activity'>
	<h3 class='tagdashboards-container-title'><?php echo ucfirst($vars['group']); ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $dashboard_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		var subtypes = $.parseJSON('<?php echo $subtypes;?>');
		elgg.tagdashboards.load_tagdashboards_custom_content("<?php echo $vars['group']; ?>", "<?php echo $vars['search']; ?>", subtypes, null);
	});
</script>