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

// Endpoint url
$end_url = elgg_get_site_url() . "pg/ubertags/ajax_load_subtype?search={$vars['search']}&subtype={$vars['subtype']}";

?>
<div class='ubertags_subtype_container'>
	<h3 class='ubertags_subtype_title'><?php echo elgg_echo('item:object:' . $vars['subtype']); ?></h3>
	<div id='<?php echo $uber_id; ?>'>
		<div id="ubertags_loading">
			<img src="<?php echo elgg_get_site_url() . "_graphics/ajax_loader_bw.gif"; ?>" />
		</div>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		function load_ubertags_subtype_content() {
			$("#<?php echo $uber_id; ?>").load("<?php echo $end_url; ?>");
		}
		load_ubertags_subtype_content();
	});
</script>