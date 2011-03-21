<?php
/**
 * Ubertags search form
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$type = $vars['type'];

// Get site tags
$site_tags = elgg_get_tags(array(threshold=>0, limit=>100));
$tags_array = array();
foreach ($site_tags as $site_tag) {
	$tags_array[] = $site_tag->tag;
}

$tags_json = json_encode($tags_array);

$search_input = elgg_view('input/text', array(	
	'internalname' => 'ubertags_search', 
	'internalid' => 'ubertags-search-input',
	'class' => 'ubertags-text-input',
	'value' => get_input('ubertags_search')
));

$submit_value = elgg_echo('ubertags:label:submitsearch');
$search_submit = "<input type='submit' 
						 name='ubertags_search_submit' 
						 id='ubertags-search-submit' 
						 value='$submit_value'
						  />";	
						
$save_form = elgg_view('forms/ubertags/save', array('search' => $vars['search']));

$save_text = elgg_echo('ubertags:label:saveform');

$save_link = "<a id='ubertags-options-toggle' href='#'>" . $save_text . " &#9660;</a>";
	
$form_body = <<<EOT
	<div id='ubertags-search-container'>
		<div>	
			$search_input $search_submit<br />
			<span id='ubertags-search-error'></span>
		</div>
	</div>
	$save_link
	$save_form
	<div id='ubertags-content-container'>
	</div>
EOT;
	 
$script = <<<EOT
	<script type='text/javascript'>	
		if (!window.location.hash) {
			$('a#ubertags-options-toggle').hide();
			$('#ubertags-save-input-container').hide();
		}
		$(document).ready(function() {	
			
			$('#ubertags-save-container').hide();
			var on = true;
			$('#ubertags-options-toggle').click(
				function() {
					// Populate the title with the search by default
					$('#ubertag_title').val(elgg.ubertags.get_ubertag_search_value);
					if (on) {
						on = false;
						$('#ubertags-options-toggle').html("$save_text" + " &#9650;");
					} else {
						on = true;
						$('#ubertags-options-toggle').html("$save_text" + " &#9660;");
					}
					console.log(on);
					$('#ubertags-save-container').toggle('slow');
					return false;
				}
			);
				
			// If we have a hash up in the address, search automatically
			if (window.location.hash) {
				var hash = decodeURI(window.location.hash.substring(1));
				var value = $('#ubertags-search-input').val(hash);
				elgg.ubertags.submit_search(hash, '$type');
				// Show the save link
			}
		
			$('#ubertags-search-submit').click(function(){
				elgg.ubertags.submit_search(elgg.ubertags.get_ubertag_search_value(), '$type');
			});
			
			$('#ubertags-search-input').keypress(function(e){
				if(e.which == 13) {
			    	elgg.ubertags.submit_search(elgg.ubertags.get_ubertag_search_value(), '$type');
					e.preventDefault();
					return false;
				}
			});
			
			// Typeahead
			var data = $.parseJSON('$tags_json');
			$("#ubertags-search-input").autocomplete(data, {
											highlight: false,
											multiple: false,
											multipleSeparator: ", ",
											scroll: true,
											scrollHeight: 300
			});	
			

		});
	</script>

EOT;

echo $form_body . $script;