<?php
/**
 * Ubertags custom tags form
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get site tags
$site_tags = elgg_get_tags(array(threshold=>0, limit=>100));
$custom_tags_array = array();
foreach ($site_tags as $site_tag) {
	$custom_tags_array[] = $site_tag->tag;
}

$custom_tags_json = json_encode($custom_tags_array);

$custom_input = elgg_view('input/text', array(
	'internalname' => 'ubertags_custom',
	'internalid' => 'ubertags-custom-input',
	'class' => 'ubertags-text-input',
));
	
echo "
<div>	
	$custom_input <br /><br />
</div>
<script type='text/javascript'>
	$(document).ready(function() {	
		// Typeahead for custom
		var data = $.parseJSON('$custom_tags_json');
		$('#ubertags-custom-input').autocomplete(data, {
				highlight: false,
				multiple: true,
				multipleSeparator: ', ',
				scroll: true,
				scrollHeight: 300
		});
	});
</script>
";

