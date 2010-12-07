<?php
/**
 * Ubertags search container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// If we weren't supplied an array of subtypes, use defaults
if (!$subtypes = $vars['subtypes']) {
	$subtypes = ubertags_get_enabled_subtypes();
}

// Loop over and display each
foreach ($subtypes as $subtype) {
	$results .= elgg_view('ubertags/ubertags_generic_container', array('subtype' => $subtype, 'search' => $vars['search']));
}

// Endpoint url
$end_url = elgg_get_site_url() . "pg/ubertags/ajax_load_subtype";

$script = <<<EOT
	<script type='text/javascript'>	
		function load_ubertags_subtype_content(subtype, search, offset) {
			var end_url = "$end_url";
			end_url += "?subtype=" + subtype + "&search=" + search;
			if (offset) {
				end_url += "&offset=" + offset;
			}
			$("#loading_" + subtype).show();
			$("#" + subtype + "_content").hide();
			$("#" + subtype + "_content").load(end_url, '', function () {
				$("#loading_" + subtype).fadeOut('fast', function () {
					$("#" + subtype + "_content").fadeIn('fast');
				});
			});
		
			return false;
		}

	</script>
EOT;

echo $script . $results . "<div style='clear: both;'></div>";
