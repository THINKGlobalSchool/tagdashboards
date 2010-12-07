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
	<script type='text/javascript'>
		$(document).ready(function() {
			function load_ubertags_results(search) {
				$('#ubertags_load_results').load('$results_end_url' + '?search=' + search);
				return false;
			}
			
			function submit_search() {
				var value = $('#ubertags_search_input').val();
				if (value) {
					load_ubertags_results(value);
					$('a#show_hide').show();
					$('span#ubertags_search_error').html('');
				} else {
					$('a#show_hide').hide();
					$('span#ubertags_search_error').html('Please enter text to search');
					$('#ubertags_load_results').html('');
				}
			}
		
			$('#ubertags_search_submit').click(function(){
				submit_search();
			});
			
			$('#ubertags_search_input').keypress(function(e){
				if(e.which == 13) {
			    	submit_search();
					e.preventDefault();
					return false;
					
				}
			});
			
			
		});
	</script>

EOT;

echo $form_body . $script;