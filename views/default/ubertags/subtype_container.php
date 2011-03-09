<?php
/**
 * Ubertags generic container
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Div, JS label
$uber_id = $vars['subtype'] . '_content';

// Check if anyone wants to change the heading for their subtype
$subtype_heading = trigger_plugin_hook('ubertags:subtype:heading', $vars['subtype'], array(), false);
if (!$subtype_heading) {
	// Use default item:object:subtype as this is usually defined 
	$subtype_heading = elgg_echo('item:object:' . $vars['subtype']);
}

$spinner = elgg_view('ubertags/ajax_spinner', array('id' => 'loading_' . $vars['subtype']));

?>
<div class='ubertags-subtype-container'>
	<h3 class='ubertags-subtype-title'><?php echo $subtype_heading; ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $uber_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		elgg.ubertags.load_ubertags_subtype_content("<?php echo $vars['subtype']; ?>", "<?php echo $vars['search']; ?>", null);
	});
</script>