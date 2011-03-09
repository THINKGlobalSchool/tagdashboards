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
	'value' => get_input('ubertags_search')
));

$submit_value = elgg_echo('ubertags:label:submitsearch');
$search_submit = "<input type='submit' 
						 name='ubertags_search_submit' 
						 id='ubertags-search-submit' 
						 value='$submit_value'
						  />";


$form_body = <<<EOT
	<div>	
		$search_input $search_submit<br />
		<span id='ubertags-search-error'></span>
	</div>
EOT;

$script = <<<EOT
	<script type='text/javascript'>
		$(document).ready(function() {			
			// If we have a hash up in the address, search automatically
			if (window.location.hash) {
				var hash = decodeURI(window.location.hash.substring(1));
				var value = $('#ubertags-search-input').val(hash);
				elgg.ubertags.submit_search(hash);
				// Show the save link
			}
		
			$('#ubertags-search-submit').click(function(){
				elgg.ubertags.submit_search(elgg.ubertags.get_ubertag_search_value());
			});
			
			$('#ubertags-search-input').keypress(function(e){
				if(e.which == 13) {
			    	elgg.ubertags.submit_search(elgg.ubertags.get_ubertag_search_value());
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