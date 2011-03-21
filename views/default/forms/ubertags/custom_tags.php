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

$custom_tags = $vars['value'];
$custom_value = '';
// Create tag string from supplied value
if ($custom_tags) {
	if (!is_array($custom_tags)) {
		$custom_tags = array($custom_tags);
	}

	foreach($custom_tags as $tag) {
		if (!empty($custom_value)) {
			$custom_value .= ", ";
		}
		if (is_string($tag)) {
			$custom_value .= $tag;
		}
	}
}

// Load sticky form values
if (elgg_is_sticky_form('ubertags_save_form')) {
	$custom_value = elgg_get_sticky_value('ubertags_save_form', 'ubertags_custom');
}

$custom_input = elgg_view('input/text', array(
	'internalname' => 'ubertags_custom',
	'internalid' => 'ubertags-custom-input',
	'class' => 'ubertags-text-input',
	'value' => $custom_value,
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

