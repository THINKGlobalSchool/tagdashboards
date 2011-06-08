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
			
	$timeline_load = elgg_get_site_url() . "tagdashboards/loadtimeline/" . $tagdashboard->getGUID();
	$timeline_data = elgg_get_site_url() . "tagdashboards/timelinefeed/" . $tagdashboard->getGUID();

	$last_entity = tagdashboards_get_last_content($tagdashboard->getGUID());
	if ($last_entity) {
		$latest_date = date('r', strtotime(strftime("%a %b %d %Y", $last_entity->time_created))); 
	}
		
	// Tag Dashboard Content inputs
	$td_type_input = elgg_view('input/hidden', array(
		'name' => 'type', 
		'id' => 'type', 
		'value' => $tagdashboard->groupby)
	);
	
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
	
	
	
	$content = <<<HTML
		<div class='tagdashboard-big-title'>
			$title
		</div>
		<div class='tagdashboard-description'>
			<i>$searchtag_label: $searchtag_content</i><br /><br />
			$description
		</div>
		<div class='tagdashboard-view-block'>
			<a class='switch-tagdashboards' id='switch-content'>$content_link_label</a> / <a class='switch-tagdashboards' id='switch-timeline'>$timeline_link_label</a>
		</div>
		<div class='tagdashboard-comment-block'>
			$edit_link $comments_link
		</div>
		<div style='clear:both;'></div>
		<div id='tagdashboards-timeline-container'></div>
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
			<div class='tagdashboards-content-container'></div>
		</div>
		<a name='annotations'></a><hr style='border: 1px solid #bbb' />
HTML;


	$script = <<<HTML
		<script type='text/javascript'>
		
			// This is all timeline related 
			var is_tl_loaded = false;
			var end_url = "$timeline_load";

			setTimelineDataURL("$timeline_data");
			setLatestDate("$latest_date");

			$("#tagdashboards-timeline-container").resize(function () {
				$(".tagdashboards-content-container").css({top: -(	$("#tagdashboards-timeline-container").height())});
			});

			// Grab height of the timeline container initially
			tl_height = $('#tagdashboards-timeline-container').height();

			// Set the top position of the content container to -(tl_heigh)
			$(".tagdashboards-content-container").css({top: -(tl_height)});


			if (window.location.hash) {
				var hash = decodeURI(window.location.hash.substring(1));
				if (hash == "timeline") {
					loadTimeline();
				}
			}

			$('.switch-tagdashboards').click(function () {
				if ($(this).attr('id') == "switch-content") {
					window.location.hash = "";
					$("#tagdashboards-timeline-container").css({visibility: 'hidden'});
					$(".tagdashboards-content-container").show();
				} else if ($(this).attr('id') == "switch-timeline") {
					window.location.hash = "timeline";
					loadTimeline();
				}
			});

			function loadTimeline() {
				if (!is_tl_loaded) {
					$("#tagdashboards-timeline-container").load(end_url, function() {
						is_tl_loaded = true;
						onLoad(); // init timeline
					});
				}
				$("#tagdashboards-timeline-container").css({visibility: 'visible'});
				$(".tagdashboards-content-container").hide();
			}
		</script>
HTML;

	$subtitle = "<p>$author_text $date $comments_link</p>";

	$params = array(
		'entity' => $tagdashboard,
		'metadata' => $metadata,
		'subtitle' => $subtitle,
		'tags' => $tags,
		'content' => $content . $script,
	);
	
	$list_body = elgg_view('page/components/summary', $params);

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

	$list_body = elgg_view('page/components/summary', $params);

	echo elgg_view_image_block($owner_icon, $list_body);
}