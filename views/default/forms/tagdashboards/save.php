<?php
/**
 * Tag Dashboards save form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Get values/sticky values
$title 			= elgg_extract('title', $vars);
$description 	= elgg_extract('description', $vars);
$tags 			= elgg_extract('tags', $vars);
$access_id 		= elgg_extract('access_id', $vars);
$search			= elgg_extract('search', $vars);
$custom_tags	= elgg_extract('custom_tags', $vars);
$owner_guids 	= elgg_extract('owner_guids', $vars);
$lower_date 	= elgg_extract('lower_date', $vars);
$upper_date 	= elgg_extract('upper_date', $vars);
$container_guid = elgg_extract('container_guid', $vars);
$groupby 		= elgg_extract('groupby', $vars);
$guid 		 	= elgg_extract('guid', $vars);
		
// If we have an entity, we're editing
if ($guid) {
	$tagdashboard = get_entity($guid);
	// Make sure metadata is set
	if ($tagdashboard->subtypes) {
		$enabled = unserialize($tagdashboard->subtypes);
	} else {
		$enabled = tagdashboards_get_enabled_subtypes();
	}
	
	// Hidden field to identify tagdashboard
	$tagdashboard_guid 	= elgg_view('input/hidden', array(
		'id' => 'tagdashboard-guid', 
		'name' => 'guid',
		'value' => $guid,
	));
			
	// Show the form automatically
	$display_form = 'block';
	
} else { // Creating a new tagdashboard
	$enabled = tagdashboards_get_enabled_subtypes();
	$access_id = ACCESS_LOGGED_IN;
	
	// Hidden search input
	$hidden_search_input = elgg_view('input/hidden', array(
		'id' => 'tagdashboard-search',
		'name' => 'search',
		'value' => '' // Will be updated by JS
	));
	
	$display_form = 'none';
	
	$tagdashboards_refresh_input = elgg_view('input/submit', array(
		'id' => 'tagdashboards-refresh-input',
		'name' => 'tagdashboards_refresh_input',
		'value' => elgg_echo('tagdashboards:label:refresh')
	));
}

$search_input = elgg_view('input/text', array(	
	'name' => 'search', 
	'id' => 'tagdashboards-search-input',
	'class' => 'tagdashboards-text-input tagdashboards-autocomplete-tags',
	'value' => $search,
));

$search_submit = elgg_view('input/submit', array(
	'name' => 'search_submit',
	'id' => 'tagdashboards-search-submit',
	'value' => elgg_echo('tagdashboards:label:submitsearch'),
));
						
$save_text = elgg_echo('tagdashboards:label:saveform');
$save_link = "<a id='tagdashboards-options-toggle' href='#'>" . $save_text . " &#9660;</a>";

$container_guid_input = elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $container_guid,
));

// For the groupby input
$selected_tab = "tab-$groupby";	

$subtypes = tagdashboards_get_enabled_subtypes();

// Labels/Inputs
$title_label = elgg_echo('tagdashboards:label:title');
$title_input = elgg_view('input/text', array(
	'id' => 'tagdashboard-title',
	'name' => 'title',
	'value' => $title
));

$description_label =  elgg_echo('tagdashboards:label:description');
$description_input = elgg_view('input/longtext', array(
	'id' => 'tagdashboard-description',
	'name' => 'description',
	'value' => $description
));

$tags_label =  elgg_echo('tagdashboards:label:tags');
$tags_input = elgg_view('input/tags', array(
	'id' => 'tagdashboard-tags',
	'name' => 'tags',
	'value' => $tags
));

$access_label =  elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'id' => 'tagdashboard-access',
	'name' => 'access_id',
	'value' => $access_id
));

$tagdashboards_save_input = elgg_view('input/submit', array(
	'id' => 'tagdashboards-save-input',
	'name' => 'tagdashboards_save_input',
	'value' => elgg_echo('tagdashboards:label:save')
));


$subtypes_label = elgg_echo('tagdashboards:label:contenttypes');
$subtypes_input = '';

foreach($subtypes as $subtype) {
	$label = trigger_plugin_hook('tagdashboards:subtype:heading', $subtype, array(), $subtype);
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked='checked'";
	}
	$subtypes_input .= "<div class='enabled-content-type'>";
	$subtypes_input .= "<label>$label</label>";
	$subtypes_input .= "<input class='tagdashboards-subtype-input' type='checkbox' name='subtypes[]' value='$subtype' $checked />";
	$subtypes_input .= "</div>";
}

// Build grouping content
$grouping_label = elgg_echo('tagdashboards:label:grouping');

$group_subtype = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:subtype')
));

$group_activity = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:activity')
));

$group_custom = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:custom'), 
	'form' => elgg_view('forms/tagdashboards/custom_tags', array('value' => $custom_tags))
));

// Build up tab array with id's, labels, and content	
$tabs = array(
	array(
		'id' => 'tab-subtype', 
		'label' => elgg_echo('tagdashboards:label:subtype'), 
		'content' => $group_subtype,
		'value' => 'subtype'
	),
	array(
		'id' => 'tab-activity', 
		'label' => elgg_echo('tagdashboards:label:activity'), 
		'content' => $group_activity,
		'value' => 'activity',
	),
	array(
		'id' => 'tab-custom', 
		'label' => elgg_echo('tagdashboards:label:customtags'), 
		'content' => $group_custom,
		'value' => 'custom',
	)
);

// Build tab nav and content
for ($i = 0; $i < count($tabs); $i++) {
	// Tab Nav
	$selected = ($selected_tab == $tabs[$i]['id']) ? "checked='checked'" : ""; 
	$tab_items .= "<div onclick=\"javascript:elgg.tagdashboards.tagdashboards_switch_groupby('{$tabs[$i]['id']}', '{$tabs[$i]['value']}')\" class='enabled-content-type'><label style='cursor: pointer;'>{$tabs[$i]['label']}</label><input id='{$tabs[$i]['id']}' class='tagdashboards-groupby-radio' type='radio' $selected /></div>";
	// Tab Content
	$tab_content .= "<div class='tagdashboards-groupby-div' id='{$tabs[$i]['id']}'>{$tabs[$i]['content']}</div>";
}

$hidden_groupby_input = elgg_view('input/hidden', array(
	'id' => 'tagdashboard-groupby',
	'name' => 'groupby',
	'value' => $groupby, // default
));

$filter_label = elgg_echo('tagdashboards:label:filter');
$filter_owners .= elgg_echo('tagdashboards:label:filterowner');
$filter_owners_input .= elgg_view('input/userpicker', array(
	'name' => 'owner_guids',
	'value' => $owner_guids,
));

$filter_date = elgg_echo('tagdashboards:label:filterdate');
$filter_date_input .= elgg_view('input/tddaterange', array(
	'name' => 'tagdashboard_date_range',
	'id' => 'tagdashboard-date-range',
	'value_lower' => $lower_date,
	'value_upper' => $upper_date,
));

$form_body = <<<HTML
	<div id='tagdashboards-search-container'>
		<div>	
			$search_input $search_submit<br />
			<span id='tagdashboards-search-error'></span>
		</div>
	</div>
	$save_link
	<div id='tagdashboards-save-container' style='display: $display_form;'>
		$search_content
		<p>
			<label>$title_label</label>
			$title_input
		</p>
		<p>
			<label>$description_label</label>
			$description_input
		</p>
		<p>
			<a id='tagdashboards-subtypes-toggler' class='tagdashboards-toggler' href='#'>$subtypes_label &#9660;</a><br />
			<div id='tagdashboards-subtypes-input' style='display: none; clear: both;'>
				$subtypes_input
				<div style='clear: both;'></div>
			</div>
		</p>
		<br />
		<p>
			<a id='tagdashboards-groupby-toggler' class='tagdashboards-toggler' href='#'>$grouping_label &#9660;</a><br />
			<div id='tagdashboards-groupby-input' style='display: none; clear: both;'>
				$tab_items
				<br /><br />
				$tab_content
			</div>
		</p>
		<br />
		<p>
			<a id='tagdashboards-filter-toggler' class='tagdashboards-toggler' href='#'>$filter_label &#9660;</a><br />
			<div id='tagdashboards-filter-input' style='display: none; clear: both;'>
				<strong>$filter_owners</strong><br /><br />
				$filter_owners_input
				<div style='clear: both;'></div><br />
				<strong>$filter_date</strong><br /><br />
				$filter_date_input
			</div>
		</p>
		<div style='clear: both;'></div>
		<br />
		<p>
			<label>$tags_label</label>
			$tags_input
		</p>
		<br />
		<p>
			<label>$access_label</label>
			$access_input
		</p>
	</div>
	<p>
		<div id="tagdashboards-save-input-container">
			$tagdashboards_refresh_input
			$tagdashboards_save_input
		</div>
		$container_guid_input
		$hidden_search_input
		$hidden_groupby_input
		$tagdashboard_guid
	</p>
	<div id='tagdashboards-content-container'>
	</div>
	<script type="text/javascript">
		var subtypes_on = false;
		var groupby_on = false;
		var filter_on = false;
	
		$(document).ready(function() {
			// Need to force this on load, for some reason, sometimes, the browser will remember the wrong tab
			$('#tagdashboard-groupby').val('$groupby');

						
			$("div#$selected_tab").show();
		});
		
		$('#tagdashboards-save-input').click(function() {
			$('input#tagdashboard-search').val(elgg.tagdashboards.get_tagdashboard_search_value());
		});
		
		$('#tagdashboards-subtypes-toggler').click(function () {
			if (subtypes_on) {
				subtypes_on = false;
				$('#tagdashboards-subtypes-toggler').html("$subtypes_label" + " &#9660;");
			} else {
				subtypes_on = true;
				$('#tagdashboards-subtypes-toggler').html("$subtypes_label" + " &#9650;");
			}
			$('#tagdashboards-subtypes-input').toggle('slow');
			return false;
		});
		
		$('#tagdashboards-groupby-toggler').click(function () {
			if (groupby_on) {
				groupby_on = false;
				$('#tagdashboards-groupby-toggler').html("$grouping_label" + " &#9660;");
			} else {
				groupby_on = true;
				$('#tagdashboards-groupby-toggler').html("$grouping_label" + " &#9650;");
			}
			$('#tagdashboards-groupby-input').toggle('slow');
			return false;
		});
		
		$('#tagdashboards-filter-toggler').click(function () {
			if (filter_on) {
				filter_on = false;
				$('#tagdashboards-filter-toggler').html("$filter_label" + " &#9660;");
			} else {
				filter_on = true;
				$('#tagdashboards-filter-toggler').html("$filter_label" + " &#9650;");
			}
			$('#tagdashboards-filter-input').toggle('slow');
			return false;
		});
		
		$('#tagdashboards-refresh-input').click(function() {
			// Grab selected subtypes
			var inputs = $('.tagdashboards-subtype-input:checked');
			var selected_subtypes = {};
			count = 0;
			inputs.each(function() {
				selected_subtypes[count] = $(this).val();
				count++;
			});
			
			var search = $("#tagdashboards-search-input").val();
			
			if (!search) {
				search = elgg.tagdashboards.get_tagdashboard_search_value();
			}
			
			// Get owner guids
			var userpicker_input = $('input[name="owner_guids[]"]');
			var owner_guids = new Array();
			count = 0;
			userpicker_input.each(function() {
				owner_guids[count] = $(this).val();
				count++;
			});
			
			// Set up options
			var options = new Array();
			options['search'] = search;
			options['type'] = $('#tagdashboard-groupby').val();
			options['subtypes'] = selected_subtypes;
			options['custom_tags'] = $('#tagdashboards-custom-input').val();
			options['owner_guids'] = owner_guids;
			
			// Get/parse dates
			options['lower_date'] = Date.parse($('#tagdashboard-date-range-from').val()) / 1000;
			options['upper_date'] = Date.parse($('#tagdashboard-date-range-to').val()) / 1000;
		
			elgg.tagdashboards.display(options);
			return false;
		});
	</script>
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
				options['type'] = '$groupby';
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
				options['type'] = '$groupby';
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
		});
	</script>
HTML;


echo $form_body;
