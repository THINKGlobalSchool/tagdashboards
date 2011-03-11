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
	
	// Load hidden input
	$script = <<<EOT
		<script type='text/javascript'>
			// Load our hidden input so we can save the search term
			$(document).ready(function() {
				$('input#ubertag_search').val($('#ubertags-search-input').val());
			});
		</script>
EOT;


	// Hidden search input
	$hidden_search_input = elgg_view('input/hidden', array(
		'internalid' => 'ubertag_search',
		'internalname' => 'ubertag_search',
		'value' => '' // Will be updated by JS
	));
}

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
		$checked = "checked";
	}
	$subtypes_input .= "<div class='enabled-content-type'>";
	$subtypes_input .= "<label>$subtype</label>";
	$subtypes_input .= "<input type='checkbox' name='subtypes_enabled[]' value='$subtype' $checked />";
	$subtypes_input .= "</div>";
}

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
			$ubertags_save_input
			$hidden_search_input
			$ubertag_guid
		</p>
	</div>
EOT;


echo elgg_view('input/form', array(
	'internalname' => 'ubertags_save_form',
	'internalid' => 'ubertags_save_form',
	'body' => $form_body,
	'action' => $action
)) . $script;
