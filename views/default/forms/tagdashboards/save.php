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
$users          = elgg_extract('users', $vars);
$owner_guids 	= elgg_extract('owner_guids', $vars);
$lower_date 	= elgg_extract('lower_date', $vars);
$upper_date 	= elgg_extract('upper_date', $vars);
$container_guid = elgg_extract('container_guid', $vars, elgg_get_page_owner_guid());
$groupby 		= elgg_extract('groupby', $vars);
$guid 		 	= elgg_extract('guid', $vars);
$column_count 	= elgg_extract('column_count', $vars);
		
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
	$display_form = 'none';
}

$search_input = elgg_view('input/tags', array(	
	'name' => 'search', 
	'id' => 'tagdashboards-search-input',
	'value' => $search,
));

$search_submit = elgg_view('input/submit', array(
	'name' => 'search_submit',
	'id' => 'tagdashboards-search-submit',
	'value' => elgg_echo('tagdashboards:label:submitsearch'),
));

$container_guid_input = elgg_view('input/hidden', array(
	'name' => 'container_guid',
	'value' => $container_guid,
));

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

$tagdashboards_refresh_input = elgg_view('input/submit', array(
	'id' => 'tagdashboards-refresh-input',
	'name' => 'tagdashboards_refresh_input',
	'value' => elgg_echo('tagdashboards:label:refresh')
));

$subtypes_input = '';

foreach($subtypes as $subtype) {
	$label = elgg_trigger_plugin_hook('tagdashboards:subtype:heading', $subtype, array(), $subtype);
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked";
	}
	$subtypes_input .= "<div class='enabled-content-type'>";
	$subtypes_input .= "<label>$label</label>";	
	$subtypes_input .= "<input class='tagdashboards-subtype-input' type='checkbox' name='subtypes[]' value='$subtype' $checked />";
	$subtypes_input .= "</div>";
}

// Build grouping content
$group_subtype = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:subtype')
));


$activity_description = "<ul>";

$activities = tagdashboards_get_activities();

foreach ($activities as $activity) {
	$activity_description .= "<li>" . $activity['name'] . "</li>";
}

$activity_description .= "</ul><br />";

$group_activity = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:activity', array($activity_description))
));

$group_custom = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:custom'), 
	'form' => elgg_view('forms/tagdashboards/custom_tags', array('value' => $custom_tags))
));

$group_users = elgg_view('tagdashboards/groupby', array(
	'description' => elgg_echo('tagdashboards:description:users'), 
	'form' => elgg_view('forms/tagdashboards/users', array('value' => $users))
));


$groupby_input = elgg_view('input/radio', array(
	'id' => 'tagdashboard-groupby-input',
	'name' => 'groupby',
	'options' => array(
		elgg_echo('tagdashboards:label:subtype') => 'subtype',
		elgg_echo('tagdashboards:label:activity') => 'activity',
		elgg_echo('tagdashboards:label:customtags') => 'custom',
		elgg_echo('tagdashboards:label:users') => 'users'
	),
	'value' => $groupby,
	'class' => 'elgg-input-radio elgg-horizontal tagdashboards-groupby-radio',
));

$column_checked = ($column_count && $column_count < 2) ? 'checked' : NULL;
if ($column_checked) {
	$float = 'no-float';
}

$column_label = elgg_echo('tagdashboards:label:columns');
$column_input = elgg_view('input/checkbox', array(
	'name' => 'columns',
	'checked' => $column_checked,
	'class' => 'tagdashboards-check-column',
));

$filter_owners .= elgg_echo('tagdashboards:label:filterowner');
$filter_owners_input .= elgg_view('input/userpicker', array(
	'value' => $owner_guids,
));

$filter_date = elgg_echo('tagdashboards:label:filterdate');
$filter_date_input .= elgg_view('input/tddaterange', array(
	'name' => 'tagdashboard_date_range',
	'id' => 'tagdashboard-date-range',
	'value_lower' => $lower_date,
	'value_upper' => $upper_date,
));

// Togglers
$subtypes_label = elgg_echo('tagdashboards:label:contenttypes');
$groupby_label = elgg_echo('tagdashboards:label:grouping');
$filter_label = elgg_echo('tagdashboards:label:filter');
$save_label = elgg_echo('tagdashboards:label:saveform');			
$save_link = "<a style='display: $display_form;' class='tagdashboards-arrow-toggler' id='tagdashboards-options-toggle' name='{$save_label}' href='#tagdashboards-save-container'></a>";

$form_body = <<<HTML
	<div id='tagdashboards-search-container'>
		<div class='tagdashboards-search-left'>
			$search_input
		</div>
		<div class='tagdashboards-search-right'>
			$search_submit
		</div>
	</div>
	<br />
	<div class='clearfix'><span id='tagdashboards-search-error'></span></div>
	<p>$save_link</p>
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
			<a id='tagdashboards-subtypes-toggler' name='$subtypes_label' class='tagdashboards-toggler tagdashboards-arrow-toggler' href='#tagdashboards-subtypes-input'></a><br />
			<div id='tagdashboards-subtypes-input' style='display: none; clear: both;'>
				$subtypes_input
				<div style='clear: both;'></div>
			</div>
		</p>
		<br />
		<p>
			<a id='tagdashboards-groupby-toggler' name='$groupby_label' class='tagdashboards-toggler tagdashboards-arrow-toggler' href='#tagdashboards-groupby-input'></a><br />
			<div id='tagdashboards-groupby-input' style='display: none; clear: both;'>
				$groupby_input
				<br />
				<div class='tagdashboards-groupby-div' id='tagdashboards-groupby-div-subtype'>$group_subtype</div>
				<div class='tagdashboards-groupby-div' id='tagdashboards-groupby-div-activity'>$group_activity</div>
				<div class='tagdashboards-groupby-div' id='tagdashboards-groupby-div-custom'>$group_custom</div>
				<div class='tagdashboards-groupby-div' id='tagdashboards-groupby-div-users'>$group_users</div>
				<div>
					<label>$column_label</label>
					$column_input
				</div>
			</div>
		</p>
		<br />
		<p>
			<a id='tagdashboards-filter-toggler' name='$filter_label' class='tagdashboards-toggler tagdashboards-arrow-toggler' href='#tagdashboards-filter-input'></a><br />
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

	<div class="elgg-foot" style='display: $display_form;' id="tagdashboards-save-input-container">
		$tagdashboards_refresh_input
		$tagdashboards_save_input
	</div>
	$container_guid_input
	$tagdashboard_guid
	<div class='tagdashboards-content-container $float'>
	</div>
HTML;

echo $form_body;
