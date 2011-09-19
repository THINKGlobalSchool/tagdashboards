<?php
/**
 * tagdashboard entity view
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$tagdashboard = elgg_extract('entity', $vars, FALSE);
$full = elgg_extract('full_view', $vars, FALSE);

// Check for valid entity
if (!$tagdashboard) {
	return TRUE;
}

// Get entity info
$owner = $tagdashboard->getOwnerEntity();

$container = $tagdashboard->getContainerEntity();

$title = $tagdashboard->title;

$description = $tagdashboard->description;

$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array(
	'href' => "todo/owner/$owner->username",
	'text' => $owner->name,
));

$author_text = elgg_echo('byline', array($owner_link));

$tags = elgg_view('output/tags', array('tags' => $tagdashboard->tags));

$date = elgg_view_friendly_time($tagdashboard->time_created);

$comments_count = $tagdashboard->countComments();
//only display if there are commments
if ($comments_count != 0) {
	$text = elgg_echo("comments") . " ($comments_count)";
	$comments_link = elgg_view('output/url', array(
		'href' => $tagdashboard->getURL() . '#comments',
		'text' => $text,
	));
} else {
	$comments_link = '';
}

$metadata = elgg_view_menu('entity', array(
	'entity' => $tagdashboard,
	'handler' => 'tagdashboards',
	'sort_by' => 'priority',
	'class' => 'elgg-menu-hz',
));

if ($full) { // Full view
	$subtitle = "<p>$author_text $date $comments_link</p>";
	
	$searchtag_label = elgg_echo('tagdashboards:label:searchtag');
	$searchtag_content = $tagdashboard->search;

	$content_link_label = elgg_echo('tagdashboards:label:contentview');
	$timeline_link_label = elgg_echo('tagdashboards:label:timelineview');
			
	// Display tag dashboard depending on the tag dashboards groupby option
	$subtypes = unserialize($tagdashboard->subtypes);
			
	// Tag Dashboard Content inputs
	$td_type_input = elgg_view('input/hidden', array(
		'name' => 'type', 
		'id' => 'type', 
		'value' => $tagdashboard->groupby
	));
	
	$td_search_input = elgg_view('input/hidden', array(
		'name' => 'search', 
		'id' => 'search', 
		'value' => $tagdashboard->search
	));
	
	$td_subtypes_input = elgg_view('input/hidden', array(
		'name' => 'subtypes', 
		'id' => 'subtypes', 
		'value' => json_encode($subtypes)
	));
	
	$td_custom_tags_input = elgg_view('input/hidden', array(
		'name' => 'custom_tags', 
		'id' => 'custom_tags', 
		'value' => json_encode($tagdashboard->custom_tags)
	));
	
	$td_owner_guids_input = elgg_view('input/hidden', array(
		'name' => 'owner_guids', 
		'id' => 'owner_guids', 
		'value' => json_encode($tagdashboard->owner_guids)
	));
	
	$td_lower_date_input = elgg_view('input/hidden', array(
		'name' => 'lower_date', 
		'id' => 'lower_date', 
		'value' => $tagdashboard->lower_date
	));
	
	$td_upper_date_input = elgg_view('input/hidden', array(
		'name' => 'upper_date', 
		'id' => 'lower_date', 
		'value' => $tagdashboard->upper_date
	));
	
	$td_hidden_guid = elgg_view('input/hidden', array(
		'name' => 'tagdashboard-guid', 
		'id' => 'tagdashboard-guid', 
		'value' => $tagdashboard->guid,
	));
	
	$timeline = elgg_view('tagdashboards/timeline');
	
	// Display one or two column
	if ($tagdashboard->column_count && $tagdashboard->column_count < 2) {
		$float = "no-float";
	}
	
	$content = <<<HTML
		<div class='tagdashboard-description'>
			$description
		</div>
		<div class='tagdashboard-view-block'>
			<a class='switch-tagdashboards' href='0'>$content_link_label</a> / <a class='switch-tagdashboards' href='1'>$timeline_link_label</a>
		</div>
		<div style='clear:both;'></div>
		$timeline
		<!-- This is the tag dashboard itself, it will be later initted by JS -->
		<div class='tagdashboard-container'>
			<div class='tagdashboard-options'>
				$td_type_input
				$td_search_input
				$td_subtypes_input
				$td_custom_tags_input
				$td_owner_guids_input
				$td_lower_date_input
				$td_upper_date_input
			</div>
			<div class='tagdashboards-content-container $float'></div>
		</div>
		<a name='annotations'>
		$td_hidden_guid
HTML;

	$subtitle = "<p>$author_text $date $comments_link<br /><strong>$searchtag_label: $searchtag_content</strong></p>";

	$params = array(
		'entity' => $tagdashboard,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'title' => FALSE,
		'content' => $content . $script,
	);
	
	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
	
} else { // Listing 
	if($description != '') {
		$view_desc = "| <a class='elgg-toggler' href='#desc-{$tagdashboard->guid}'>" . elgg_echo('description') . "</a>";
		$description = "<div style='display: none;' id='desc-{$tagdashboard->guid}'>$description</div>"; 
	} else {
		$view_desc = '';
	}
	
	$subtitle = "<p>$author_text $date $comments_link $view_desc</p>";

	$params = array(
		'entity' => $tagdashboard,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => $description,
	);

	$list_body = elgg_view('object/elements/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
}