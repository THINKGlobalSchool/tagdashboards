<?php
/**
 * Ubertags save form
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// If we have an entity, we're editing
if ($vars['entity']) {
	$action 		= 'action/ubertags/edit';
	$title 			= $vars['entity']->title;
	$description 	= $vars['entity']->description;
	$tags 			= $vars['entity']->tags;
	$access_id 		= $vars['entity']->access_id;
	$search			= $vars['entity']->search;
	$custom_tags	= $vars['entity']->custom_tags;
	
	// Load sticky form values
	if (elgg_is_sticky_form('ubertags_save_form')) {
		$title = elgg_get_sticky_value('ubertags_save_form', 'ubertag_title');
		$search = elgg_get_sticky_value('ubertags_save_form', 'ubertag_search');
		$description = elgg_get_sticky_value('ubertags_save_form', 'ubertag_description');
		$tags = elgg_get_sticky_value('ubertags_save_form', 'ubertag_tags');
		$access_id = elgg_get_sticky_value('ubertags_save_form', 'ubertag_access');
	}
	
	
	// Make sure metadata is set
	if ($vars['entity']->subtypes) {
		$enabled	 	= unserialize($vars['entity']->subtypes);
	} else {
		$enabled = ubertags_get_enabled_subtypes();
	}
	
	
	$search_label = elgg_echo('ubertags:label:searchtag');
	// Get site tags
	$site_tags = elgg_get_tags(array(threshold=>0, limit=>100));
	$tags_array = array();
	foreach ($site_tags as $site_tag) {
		$tags_array[] = $site_tag->tag;
	}

	$tags_json = json_encode($tags_array);

	$search_input = elgg_view('input/text', array(	
		'internalname' => 'ubertag_search', 
		'internalid' => 'ubertags-search',
		'value' => $search
	));
	
	$search_content = "
	<p>
		<br />
		<label>$search_label</label>
		$search_input
	</p>";
	
	// Hidden field to identify ubertag
	$ubertag_guid 	= elgg_view('input/hidden', array(
		'internalid' => 'ubertag_guid', 
		'internalname' => 'ubertag_guid',
		'value' => $vars['entity']->getGUID()
	));
		
	$groupby = $vars['entity']->groupby;
	
	// Possible that this isn't set, so default to subtype
	if (!$groupby) {
		$groupby = 'subtype';
	}
	
	// Typeahead tag search
	$script = <<<EOT
		<script type='text/javascript'>
			// Typeahead
			var data = $.parseJSON('$tags_json');
			$("#ubertags-search").autocomplete(data, {
											highlight: false,
											multiple: false,
											multipleSeparator: ", ",
											scroll: true,
											scrollHeight: 300
			});
		</script>
EOT;

} else { // Creating a new ubertag
	$action = 'action/ubertags/save';
	$enabled = ubertags_get_enabled_subtypes();
	$access_id = ACCESS_LOGGED_IN;
	
	// Hidden search input
	$hidden_search_input = elgg_view('input/hidden', array(
		'internalid' => 'ubertag_search',
		'internalname' => 'ubertag_search',
		'value' => '' // Will be updated by JS
	));
	
	$ubertags_refresh_input = elgg_view('input/submit', array(
		'internalid' => 'ubertags-refresh-input',
		'internalname' => 'ubertags_refresh_input',
		'value' => elgg_echo('ubertags:label:refresh')
	));
	
	// Default grouping
	$groupby = 'subtype';
}

// For the groupby input
$selected_tab = "tab_$groupby";	

$subtypes = ubertags_get_enabled_subtypes();

// Labels/Inputs
$title_label = elgg_echo('ubertags:label:title');
$title_input = elgg_view('input/text', array(
	'internalid' => 'ubertag_title',
	'internalname' => 'ubertag_title',
	'value' => $title
));

$description_label =  elgg_echo('ubertags:label:description');
$description_input = elgg_view('input/longtext', array(
	'internalid' => 'ubertag-description',
	'internalname' => 'ubertag_description',
	'value' => $description
));

$tags_label =  elgg_echo('ubertags:label:tags');
$tags_input = elgg_view('input/tags', array(
	'internalid' => 'ubertag_tags',
	'internalname' => 'ubertag_tags',
	'value' => $tags
));

$access_label =  elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'internalid' => 'ubertag_access',
	'internalname' => 'ubertag_access',
	'value' => $access_id
));

$ubertags_save_input = elgg_view('input/submit', array(
	'internalid' => 'ubertags_save_input',
	'internalname' => 'ubertags_save_input',
	'value' => elgg_echo('ubertags:label:save')
));



$subtypes_label = elgg_echo('ubertags:label:contenttypes');
$subtypes_input = '';

foreach($subtypes as $subtype) {
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked='checked'";
	}
	$subtypes_input .= "<div class='enabled-content-type'>";
	$subtypes_input .= "<label>$subtype</label>";
	$subtypes_input .= "<input class='ubertags-subtype-input' type='checkbox' name='subtypes_enabled[]' value='$subtype' $checked />";
	$subtypes_input .= "</div>";
}

// Build grouping content
$grouping_label = elgg_echo('ubertags:label:grouping');

$group_subtype = elgg_view('ubertags/groupby', array('description' => elgg_echo('ubertags:description:subtype')));
$group_activity = elgg_view('ubertags/groupby', array('description' => elgg_echo('ubertags:description:activity')));
$group_custom = elgg_view('ubertags/groupby', array(
	'description' => elgg_echo('ubertags:description:custom'), 
	'form' => elgg_view('forms/ubertags/custom_tags', array('value' => $custom_tags))
));

// Build up tab array with id's, labels, and content	
$tabs = array(
	array(
		'id' => 'tab_subtype', 
		'label' => elgg_echo('ubertags:label:subtype'), 
		'content' => $group_subtype,
		'value' => 'subtype'
	),
	array(
		'id' => 'tab_activity', 
		'label' => elgg_echo('ubertags:label:activity'), 
		'content' => $group_activity,
		'value' => 'activity',
	),
	array(
		'id' => 'tab_custom', 
		'label' => elgg_echo('ubertags:label:customtags'), 
		'content' => $group_custom,
		'value' => 'custom',
	)
);

// Build tab nav and content
for ($i = 0; $i < count($tabs); $i++) {
	// Tab Nav
	$selected = ($selected_tab == $tabs[$i]['id']) ? "selected" : ""; 
	$tab_items .= "<li id='{$tabs[$i]['id']}' class='$selected edt_tab_nav'><a href=\"javascript:elgg.ubertags.ubertags_switch_groupby('{$tabs[$i]['id']}', '{$tabs[$i]['value']}')\">{$tabs[$i]['label']}</a></li>";
	// Tab Content
	$tab_content .= "<div class='ubertags-groupby-div' id='{$tabs[$i]['id']}'>{$tabs[$i]['content']}</div>";
}

$hidden_groupby_input = elgg_view('input/hidden', array(
	'internalid' => 'ubertag-groupby',
	'internalname' => 'ubertag_groupby',
	'value' => 'subtype', // default
));

$form_body = <<<EOT
	<div id='ubertags_save'>
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
			<label>$subtypes_label</label>
			$subtypes_input
		</p>
		<br />
		<div style='clear: both; margin-top: 35px;'>
			<label>$grouping_label</label>
			<div class="elgg_horizontal_tabbed_nav margin_top">
				<ul>
					$tab_items
				</ul>
			</div>
			<br />
			$tab_content
		</div>
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
		<p>
			$ubertags_refresh_input
			$ubertags_save_input
			$hidden_search_input
			$hidden_groupby_input
			$ubertag_guid
		</p>
	</div>
	<script type="text/javascript">
		$(document).ready(function() {
			// Need to force this on load, for some reason, sometimes, the browser will remember the wrong tab
			$('#ubertag-groupby').val('$groupby');
						
			$("div#$selected_tab").show();
		});
		
		$('#ubertags_save_input').click(function() {
			$('input#ubertag_search').val(elgg.ubertags.get_ubertag_search_value());
		});
		
		$('#ubertags-refresh-input').click(function() {
			// Grab selected subtypes
			var inputs = $('.ubertags-subtype-input:checked');
			var selected_subtypes = {};
			count = 0;
			inputs.each(function() {
				selected_subtypes[count] = $(this).val();
				count++;
			});
		
			elgg.ubertags.submit_search(elgg.ubertags.get_ubertag_search_value(), $('#ubertag-groupby').val(), selected_subtypes);
			return false;
		});
	</script>
EOT;


echo elgg_view('input/form', array(
	'internalname' => 'ubertags_save_form',
	'internalid' => 'ubertags_save_form',
	'body' => $form_body,
	'action' => $action
));
