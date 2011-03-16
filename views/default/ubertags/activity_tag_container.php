<?php
/**
 * Ubertags activity tag container
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$activity = $vars['activity'];

$subtypes = json_encode($vars['subtypes']);

// Div, JS label
$uber_id = $activity['tag'] . '_content';


$spinner = elgg_view('ubertags/ajax_spinner', array('id' => 'loading_' . $activity['tag']));

?>
<div class='ubertags-container ubertags-activity'>
	<h3 class='ubertags-container-title'><?php echo $activity['name']; ?></h3>
	<?php echo $spinner; ?>
	<div id='<?php echo $uber_id; ?>'>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		var subtypes = $.parseJSON('<?php echo $subtypes;?>');
		elgg.ubertags.load_ubertags_activity_tag_content("<?php echo $activity['tag']; ?>", "<?php echo $vars['search']; ?>", subtypes, null);
	});
</script>