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

?>
<div class='ubertags_subtype_container'>
	<h3 class='ubertags_subtype_title'><?php echo $subtype_heading; ?></h3>
	<div id='<?php echo $uber_id; ?>'>
		<div id="ubertags_loading">
			<img src="<?php echo elgg_get_site_url() . "_graphics/ajax_loader_bw.gif"; ?>" />
		</div>
	</div>
</div>
<script type='text/javascript'>
	$(document).ready(function() {
		load_ubertags_subtype_content("<?php echo $vars['subtype']; ?>", "<?php echo $vars['search']; ?>", null);
	});
</script>