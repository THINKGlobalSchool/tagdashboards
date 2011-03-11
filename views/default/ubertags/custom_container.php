<?php
/**
 * Ubertags custom container
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Div, JS label
$uber_id = $vars['group'] . '_content';


$spinner = elgg_view('ubertags/ajax_spinner', array('id' => 'loading_' . $vars['group']));

?>
<div class='ubertags-container ubertags-activity'>
	<h3 class='ubertags-container-title'><?php echo ucfirst($vars['group']); ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $uber_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		elgg.ubertags.load_ubertags_custom_content("<?php echo $vars['group']; ?>", "<?php echo $vars['search']; ?>", null);
	});
</script>