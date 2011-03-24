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

// Check for valid entity
if (elgg_instanceof($vars['entity'], 'object', 'tagdashboard')) {
	// Get entity info
	$owner = $vars['entity']->getOwnerEntity();
	$friendlytime = elgg_view_friendly_time($vars['entity']->time_created);
	$address = $vars['entity']->getURL();
	$title = $vars['entity']->title;
	$description = $vars['entity']->description;
	
	$parsed_url = parse_url($address);

	$comments_count = elgg_count_comments($vars['entity']);
	
	if ($vars['full']) { // Full view
		// Show 'leave a comment' and comment count (if any)
		if ($comments_count) {
			$comments_link = "<a href=\"#annotations\">" . elgg_echo('tagdashboards:label:leaveacomment') . '</a> / ' . 
							"<a href='#annotations'>" . elgg_echo("comments") . " ($comments_count)</a>";
		} else {
			$comments_link = "<a href=\"#annotations\">" . elgg_echo('tagdashboards:label:leaveacomment') . "</a>";
		}
		

		if ($vars['entity']->canEdit()) {
			$edit_url = elgg_get_site_url()."pg/tagdashboards/edit/{$vars['entity']->getGUID()}/";
			$edit_link = "<span class='entity_edit'><a href=\"$edit_url\">" . elgg_echo('edit') . '</a></span> / ';
		}
		
		$searchtag_label = elgg_echo('tagdashboards:label:searchtag');
		$searchtag_content = $vars['entity']->search;

		$content_link_label = elgg_echo('tagdashboards:label:contentview');
		$timeline_link_label = elgg_echo('tagdashboards:label:timelineview');
		
		// Display tag dashboard depending on the tag dashboards groupby option
		$subtypes = unserialize($vars['entity']->subtypes);
		switch ($vars['entity']->groupby) {
			case 'activity': 
				$tagdashboard_content = elgg_view('tagdashboards/activity_tag', array(
					'search' => $vars['entity']->search, 
					'subtypes' => $subtypes
				));
			break;
			case 'custom': 
				$tagdashboard_content = elgg_view('tagdashboards/custom', array(
					'search' => $vars['entity']->search, 
					'custom' => $vars['entity']->custom_tags, 
					'subtypes' => $subtypes
				));
			break;
			default: 
			case 'subtype': 
				$tagdashboard_content = elgg_view('tagdashboards/subtypes', array(
					'search' => $vars['entity']->search, 
					'subtypes' => $subtypes
				));
			break;
		}
		
		
		$timeline_load = elgg_get_site_url() . "pg/tagdashboards/load_timeline/" . $vars['entity']->getGUID();
		$timeline_data = elgg_get_site_url() . "pg/tagdashboards/timeline_feed/" . $vars['entity']->getGUID();

		$entity = tagdashboards_get_last_content($vars['entity']->getGUID());
		if ($last_entity) {
			$latest_date = date('r', strtotime(strftime("%a %b %d %Y", $last_entity->time_created))); 
		}
		
		$content = <<<EOT
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
			<div id='tagdashboards-content-container'>
				$tagdashboard_content
			</div>
			<a name='annotations'></a><hr style='border: 1px solid #bbb' />
EOT;

		$script = <<<EOT
			<script type='text/javascript'>
				var is_tl_loaded = false;
				var end_url = "$timeline_load";

				setTimelineDataURL("$timeline_data");
				setLatestDate("$latest_date");

				$("#tagdashboards-timeline-container").resize(function () {
					$("#tagdashboards-content-container").css({top: -(	$("#tagdashboards-timeline-container").height())});
				});

				// Grab height of the timeline container initially
				tl_height = $('#tagdashboards-timeline-container').height();

				// Set the top position of the content container to -(tl_heigh)
				$("#tagdashboards-content-container").css({top: -(tl_height)});


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
						$("#tagdashboards-content-container").show();
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
					$("#tagdashboards-content-container").hide();
				}
			</script>
EOT;


		echo $content . $script;
	} else { // Listing 

		//sort out the access level for display
		$object_acl = get_readable_access_level($vars['entity']->access_id);

		// Function above works sometimes.. its weird. So load ACL name if any
		if (!$object_acl) {
			$acl = get_access_collection($vars['entity']->access_id);
			$object_acl = $acl->name;
		}

		if($description != '') {
			$view_desc = "| <a class='link' onclick=\"elgg_slide_toggle(this,'.entity_listing','.note');\">" . elgg_echo('description') . "</a>";
		} else {
			$view_desc = '';
		}
		
		if ($comments_count) {
		 	$comments_link = "<a href='{$vars['entity']->getURL()}#annotations'>" . elgg_echo("comments") . " ($comments_count)</a>";
		}

		$icon = elgg_view("profile/icon", array('entity' => $owner,'size' => 'tiny',));

		//delete/edit
		if($vars['entity']->canEdit()){
			$delete_url = elgg_get_site_url() . "action/tagdashboards/delete?guid=" . $vars['entity']->guid;
			$delete_link .= "<span class='delete_button'>" . elgg_view('output/confirmlink',array(
						'href' => $delete_url,
						'text' => elgg_echo("delete"),
						'confirm' => elgg_echo("tagdashboards:label:deleteconfirm"),
						)) . "</span>";

			$edit_url = elgg_get_site_url()."pg/tagdashboards/edit/{$vars['entity']->getGUID()}/";
			$edit_link = "<span class='entity_edit'><a href=\"$edit_url\">" . elgg_echo('edit') . '</a></span>';

			$edit .= "$edit_link $delete_link";
		}
		
		// include a view for plugins to extend
		$edit .= elgg_view("tagdashboards/options",array('entity' => $vars['entity']));
		
		// Add favorites and likes
		$favorites .= elgg_view("favorites/form",array('entity' => $vars['entity']));
		$likes .= elgg_view_likes($vars['entity']); // include likes
		
		$subtext = elgg_echo('tagdashboards:label:submitted_by', array("<a href=\"".elgg_get_site_url()."pg/tagdashboards/{$owner->username}\">{$owner->name}</a>")); 

		$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
		if (!empty($tags)) {
			$tags = '<p class="tags">' . $tags . '</p>';
		}
		
		if ($view_desc != ''){
			$note = "<div class='note hidden'>". $description . "</div>";
		}
		
		$info = <<<EOT
			<div class='entity_metadata'>
				<span>$object_acl</span>
				$edit
				$favorites
				$likes
			</div>
			<p class='entity_title'>
				<a href='$address'>$title</a>
			</p>
			<p class='entity_subtext'>
				$subtext $friendlytime $comments_link $view_desc
			</p>
			$tags
			$note
EOT;

		//display
		echo elgg_view_listing($icon, $info);

	}
} 