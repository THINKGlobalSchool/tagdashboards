<?php
/**
 * Tag Dashboards search form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$type = $vars['type'];

// Get site tags
$site_tags = elgg_get_tags(array(threshold => 0, limit => 100));
$tags_array = array();
foreach ($site_tags as $site_tag) {
	$tags_array[] = $site_tag->tag;
}

$tags_json = json_encode($tags_array);

$search_input = elgg_view('input/text', array(	
	'internalname' => 'tagdashboards_search', 
	'internalid' => 'tagdashboards-search-input',
	'class' => 'tagdashboards-text-input',
	'value' => get_input('tagdashboards_search')
));

$submit_value = elgg_echo('tagdashboards:label:submitsearch');
$search_submit = "<input type='submit' 
						 name='tagdashboards_search_submit' 
						 id='tagdashboards-search-submit' 
						 value='$submit_value'
						  />";	
						
									
$save_form = elgg_view('forms/tagdashboards/save', $vars);

$save_text = elgg_echo('tagdashboards:label:saveform');

$save_link = "<a id='tagdashboards-options-toggle' href='#'>" . $save_text . " &#9660;</a>";
	
	
$form_body = <<<HTML
	<div id='tagdashboards-search-container'>
		<div>	
			$search_input $search_submit<br />
			<span id='tagdashboards-search-error'></span>
		</div>
	</div>
	$save_link
	$save_form
	<div id='tagdashboards-content-container'>
	</div>
HTML;
	 
$script = <<<HTML
	<script type='text/javascript'>	
		if (!window.location.hash) {
			$('a#tagdashboards-options-toggle').hide();
			$('#tagdashboards-save-input-container').hide();
		}
		
		// Set the title to the searched tag
		function set_dashboard_title() {
			$('#tagdashboard-title').val(elgg.tagdashboards.get_tagdashboard_search_value);
		}
		
		// Validate and submit search
		function validate_search() {
			if (!$('#tagdashboards-search-input').val()) {
				elgg.tagdashboards.display_error('tagdashboards-search-error', 'Please enter search text');
			} else {
				$('a#tagdashboards-options-toggle').show();
				$('span#tagdashboards-search-error').html('');
				set_dashboard_title();
				
				// Set up options
				var options = new Array();
				options['search'] = elgg.tagdashboards.get_tagdashboard_search_value();
				options['type'] = '$type';
				options['custom_tags'] = $('#tagdashboards-custom-input').val();
				
				elgg.tagdashboards.display(options);
				window.location.hash = encodeURI(options['search']); // Hash magic for permalinks
			}
		}
		
		$(document).ready(function() {	
			
			$('#tagdashboards-save-container').hide();
			var on = true;
			$('#tagdashboards-options-toggle').click(
				function() {
					if (on) {
						on = false;
						$('#tagdashboards-options-toggle').html("$save_text" + " &#9650;");
					} else {
						on = true;
						$('#tagdashboards-options-toggle').html("$save_text" + " &#9660;");
					}
					$('#tagdashboards-save-container').toggle('slow');
					return false;
				}
			);
				
			// If we have a hash up in the address, search automatically
			if (window.location.hash) {
				var hash = decodeURI(window.location.hash.substring(1));
				var value = $('#tagdashboards-search-input').val(hash);
				set_dashboard_title();
				
				// Set up options
				var options = new Array();
				options['search'] = hash;
				options['type'] = '$type';
				options['custom_tags'] = $('#tagdashboards-custom-input').val();				
				
				elgg.tagdashboards.display(options);
				// Show the save link
			}
		
			$('#tagdashboards-search-submit').click(function(){
				validate_search();
			});

			$('#tagdashboards-search-input').keypress(function(e){
				if(e.which == 13) {
					validate_search();
					e.preventDefault();
					return false;
				}
			});
			
			// Typeahead
			var data = $.parseJSON('$tags_json');
			$("#tagdashboards-search-input").autocomplete(data, {
											highlight: false,
											multiple: false,
											multipleSeparator: ", ",
											scroll: true,
											scrollHeight: 300
			});	
			

		});
	</script>

HTML;

echo $form_body . $script;