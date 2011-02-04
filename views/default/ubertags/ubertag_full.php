<?php
/**
 * Ubertag entity full view
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$comments_count = elgg_count_comments($vars['entity']);
$comments_link = "<a href=\"#annotations\">" . elgg_echo('ubertags:label:leaveacomment') . elgg_echo("comments") . " (". $comments_count .")</a>";

if ($vars['entity']->canEdit()) {
	$edit_url = elgg_get_site_url()."pg/ubertags/edit/{$vars['entity']->getGUID()}/";
	$edit_link = "<span class='entity_edit'><a href=\"$edit_url\">" . elgg_echo('edit') . '</a></span> / ';
}


$content = "<div class='ubertag-big-title'>" . $vars['entity']->title . "</div>";
$content .= "<div class='ubertag-description'>" . $vars['entity']->description . "</div>";
$content .= "<div class='ubertag-view-block'><a class='switch-ubertags' id='switch-content'>Content View</a> / <a class='switch-ubertags' id='switch-timeline'>Timeline View</a></div>";
$content .= "<div class='ubertag-comment-block'>" . $edit_link . $comments_link . "</div><div style='clear:both;'></div>";
$content .= "<div id='ubertags-timeline-container'>" . elgg_view('ubertags/timeline', array('entity' => $vars['entity'])) .  "</div>";
$content .= "<div id='ubertags-content-container'>" . elgg_view('ubertags/ubertags_list_results', array('search' => $vars['entity']->search, 'subtypes' => $vars['entity']->subtypes)) . "</div>";
$content .= "<a name='annotations'></a><hr style='border: 1px solid #bbb' />";

$script = <<<EOT
	<script type='text/javascript'>
		
		$("#ubertags-timeline-container").resize(function () {
			$("#ubertags-content-container").css({top: -(	$("#ubertags-timeline-container").height())});
		});
	
	
		// Grab height of the timeline container initially
		tl_height = $('#ubertags-timeline-container').height();
		
		// Set the top position of the content container to -(tl_heigh)
		$("#ubertags-content-container").css({top: -(tl_height)});
	
		$('.switch-ubertags').click(function () {
			if ($(this).attr('id') == "switch-content") {
				$("#ubertags-timeline-container").css({visibility: 'hidden'});
				$("#ubertags-content-container").show();
			} else if ($(this).attr('id') == "switch-timeline") {
				$("#ubertags-timeline-container").css({visibility: 'visible'});
				$("#ubertags-content-container").hide();
			}
		});
	</script>
EOT;


echo $content . $script;
?>