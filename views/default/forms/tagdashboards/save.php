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

// If we have an entity, we're editing
if ($vars['entity']) {
	$action 		= 'action/tagdashboards/edit';
	$title 			= $vars['entity']->title;
	$description 	= $vars['entity']->description;
	$tags 			= $vars['entity']->tags;
	$access_id 		= $vars['entity']->access_id;
	$search			= $vars['entity']->search;
	$custom_tags	= $vars['entity']->custom_tags;
	
	// Load sticky form values
	if (elgg_is_sticky_form('tagdashboards-save-form')) {
		$title = elgg_get_sticky_value('tagdashboards-save-form', 'tagdashboard_title');
		$search = elgg_get_sticky_value('tagdashboards-save-form', 'tagdashboard_search');
		$description = elgg_get_sticky_value('tagdashboards-save-form', 'tagdashboard_description');
		$tags = elgg_get_sticky_value('tagdashboards-save-form', 'tagdashboard_tags');
		$access_id = elgg_get_sticky_value('tagdashboards-save-form', 'tagdashboard_access');
	}
	
	
	// Make sure metadata is set
	if ($vars['entity']->subtypes) {
		$enabled	 	= unserialize($vars['entity']->subtypes);
	} else {
		$enabled = tagdashboards_get_enabled_subtypes();
	}
	
	
	$search_label = elgg_echo('tagdashboards:label:searchtag');
	// Get site tags
	$site_tags = elgg_get_tags(array(threshold=>0, limit=>100));
	$tags_array = array();
	foreach ($site_tags as $site_tag) {
		$tags_array[] = $site_tag->tag;
	}

	$tags_json = json_encode($tags_array);

	$search_input = elgg_view('input/text', array(	
		'internalname' => 'tagdashboard_search', 
		'internalid' => 'tagdashboards-search',
		'value' => $search
	));
	
	$search_content = "
	<p>
		<br />
		<label>$search_label</label>
		$search_input
	</p>";
	
	// Hidden field to identify tagdashboard
	$tagdashboard_guid 	= elgg_view('input/hidden', array(
		'internalid' => 'tagdashboard-guid', 
		'internalname' => 'tagdashboard_guid',
		'value' => $vars['entity']->getGUID()
	));
		
	$groupby = $vars['entity']->groupby;
	
	// Possible that this isn't set, so default to subtype
	if (!$groupby) {
		$groupby = 'subtype';
	}
	
	// Show the form automatically
	$display_form = 'block';
	
	// Typeahead tag search
	$script = <<<HTML
		<script type='text/javascript'>
			// Typeahead
			var data = $.parseJSON('$tags_json');
			$("#tagdashboards-search").autocomplete(data, {
											highlight: false,
											multiple: false,
											multipleSeparator: ", ",
											scroll: true,
											scrollHeight: 300
			});
		</script>
HTML;

} else { // Creating a new tagdashboard
	$action = 'action/tagdashboards/save';
	$enabled = tagdashboards_get_enabled_subtypes();
	$access_id = ACCESS_LOGGED_IN;
	
	// Hidden search input
	$hidden_search_input = elgg_view('input/hidden', array(
		'internalid' => 'tagdashboard-search',
		'internalname' => 'tagdashboard_search',
		'value' => '' // Will be updated by JS
	));
	
	// Default grouping
	$groupby = 'subtype';
	$display_form = 'none';
	
	$tagdashboards_refresh_input = elgg_view('input/submit', array(
		'internalid' => 'tagdashboards-refresh-input',
		'internalname' => 'tagdashboards_refresh_input',
		'value' => elgg_echo('tagdashboards:label:refresh')
	));
}

// For the groupby input
$selected_tab = "tab-$groupby";	

$subtypes = tagdashboards_get_enabled_subtypes();

// Labels/Inputs
$title_label = elgg_echo('tagdashboards:label:title');
$title_input = elgg_view('input/text', array(
	'internalid' => 'tagdashboard-title',
	'internalname' => 'tagdashboard_title',
	'value' => $title
));

$description_label =  elgg_echo('tagdashboards:label:description');
$description_input = elgg_view('input/longtext', array(
	'internalid' => 'tagdashboard-description',
	'internalname' => 'tagdashboard_description',
	'value' => $description
));

$tags_label =  elgg_echo('tagdashboards:label:tags');
$tags_input = elgg_view('input/tags', array(
	'internalid' => 'tagdashboard-tags',
	'internalname' => 'tagdashboard_tags',
	'value' => $tags
));

$access_label =  elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'internalid' => 'tagdashboard-access',
	'internalname' => 'tagdashboard_access',
	'value' => $access_id
));

$tagdashboards_save_input = elgg_view('input/submit', array(
	'internalid' => 'tagdashboards-save-input',
	'internalname' => 'tagdashboards_save_input',
	'value' => elgg_echo('tagdashboards:label:save')
));


$subtypes_label = elgg_echo('tagdashboards:label:contenttypes');
$subtypes_input = '';

foreach($subtypes as $subtype) {
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked='checked'";
	}
	$subtypes_input .= "<div class='enabled-content-type'>";
	$subtypes_input .= "<label>$subtype</label>";
	$subtypes_input .= "<input class='tagdashboards-subtype-input' type='checkbox' name='subtypes_enabled[]' value='$subtype' $checked />";
	$subtypes_input .= "</div>";
}

// Build grouping content
$grouping_label = elgg_echo('tagdashboards:label:grouping');

$group_subtype = elgg_view('tagdashboards/groupby', array('description' => elgg_echo('tagdashboards:description:subtype')));
$group_activity = elgg_view('tagdashboards/groupby', array('description' => elgg_echo('tagdashboards:description:activity')));
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
	'internalid' => 'tagdashboard-groupby',
	'internalname' => 'tagdashboard_groupby',
	'value' => 'subtype', // default
));

$form_body = <<<HTML
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
			<a id='tagdashboards-subtypes-toggler' href='#'>$subtypes_label &#9660;</a><br />
			<div id='tagdashboards-subtypes-input' style='display: none; clear: both;'>
				$subtypes_input
				<div style='clear: both;'></div>
			</div>
		</p>
		<br />
		<p>
			<a id='tagdashboards-groupby-toggler' href='#'>$grouping_label &#9660;</a><br />
			<div id='tagdashboards-groupby-input' style='display: none; clear: both;'>
				$tab_items
				<br /><br />
				$tab_content
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
		$hidden_search_input
		$hidden_groupby_input
		$tagdashboard_guid
	</p>
	<script type="text/javascript">
		var subtypes_on = false;
		var groupby_on = false;
	
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
		
		$('#tagdashboards-refresh-input').click(function() {
			// Grab selected subtypes
			var inputs = $('.tagdashboards-subtype-input:checked');
			var selected_subtypes = {};
			count = 0;
			inputs.each(function() {
				selected_subtypes[count] = $(this).val();
				count++;
			});
			
			var search = $("#tagdashboards-search").val();
			
			if (!search) {
				search = elgg.tagdashboards.get_tagdashboard_search_value();
			}
		
			elgg.tagdashboards.submit_search(search, $('#tagdashboard-groupby').val(), selected_subtypes);
			return false;
		});
	</script>
HTML;


echo elgg_view('input/form', array(
	'internalname' => 'tagdashboards-save-form',
	'internalid' => 'tagdashboards-save-form',
	'body' => $form_body,
	'action' => $action
));
