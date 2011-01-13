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
	'internalid' => 'ubertags_search_input',
	'value' => get_input('ubertags_search')
));

$submit_value = elgg_echo('ubertags:label:submitsearch');
$search_submit = "<input type='submit' 
						 name='ubertags_search_submit' 
						 id='ubertags_search_submit' 
						 value='$submit_value'
						  />";


$form_body = <<<EOT
	<div>	
		$search_input $search_submit<br />
		<span id='ubertags_search_error'></span>
	</div>
EOT;

$results_end_url = elgg_get_site_url() . "pg/ubertags/ajax_load_results";

$script = <<<EOT
	<script language="javascript" type="text/javascript" src="{$vars['url']}vendors/jquery/jquery.autocomplete.min.js"></script>
	<script type='text/javascript'>
		$(document).ready(function() {
			function load_ubertags_results(search) {
				$('#ubertags_load_results').hide().load('$results_end_url' + '?search=' + escape(search), function() {
					$('#ubertags_load_results').fadeIn('fast');
				});
				return false;
			}
			
			function submit_search(value) {
				if (value) {
					load_ubertags_results(value);
					$('a#show_hide').show();
					$('span#ubertags_search_error').html('');
					window.location.hash = encodeURI(value); // Hash magic for permalinks
				} else {
					$('a#show_hide').hide();
					$('span#ubertags_search_error').html('Please enter text to search');
					$('#ubertags_load_results').html('');
				}
				
			}
			
			function get_ubertag_search_value() {
				var value = $('#ubertags_search_input').val();
				value = value.toLowerCase();
				return value;
			}
			
			// If we have a hash up in the address, search automatically
			if (window.location.hash) {
				var hash = decodeURI(window.location.hash.substring(1));
				var value = $('#ubertags_search_input').val(hash);
				submit_search(hash);
			}
		
			$('#ubertags_search_submit').click(function(){
				submit_search(get_ubertag_search_value());
			});
			
			$('#ubertags_search_input').keypress(function(e){
				if(e.which == 13) {
			    	submit_search(get_ubertag_search_value());
					e.preventDefault();
					return false;
				}
			});
			
			// Typeahead
			var data = $.parseJSON('$tags_json');
			$("#ubertags_search_input").autocomplete(data, {
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