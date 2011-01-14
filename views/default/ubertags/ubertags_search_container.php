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

// Search and save forms
$search_form =  elgg_view('ubertags/forms/search');

// Build results content
$results = '';

//$show = elgg_echo('ubertags:label:showsave');
//$hide = elgg_echo('ubertags:label:hidesave');
$save_link = "<a id='show_hide' href='#'>" . elgg_echo('ubertags:label:saveform') . "</a>";
	
echo <<<EOT
	<div id='ubertags_search_container'>
		$search_form
	</div>
	$save_link
	<div id='ubertags_load_results'>
	</div>
	
	<script type='text/javascript'>
		$(document).ready(function() {
				if (!window.location.hash) {
					$('a#show_hide').hide();
				}
				$('#ubertags_save_container').hide();
				var on = true;
				$('#show_hide').click(
					function() {
						if (on) {
							on = false;
							//$('#show_hide').html('$hide');
						} else {
							on = true;
							//$('#show_hide').html('$show');
						}
						$('#ubertags_save_container').toggle('slow');
						return false;
					}
				);
		});
	</script>
EOT;
