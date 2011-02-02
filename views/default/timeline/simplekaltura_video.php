<?php
/**
 * Timeline view for simplekaltura videos
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

if (!($thumbnail_url = $vars['entity']->thumbnailUrl)) {
	$thumbnail_url = get_plugin_setting('kaltura_thumbnail_url', 'simplekaltura') . $vars['entity']->kaltura_entryid;
}

$comments_count = elgg_count_comments($vars['entity']);
$likes_count = elgg_count_likes($vars['entity']);

$id = $vars['entity']->kaltura_entryid;
$pop_url = elgg_get_site_url() . "mod/simplekaltura/popwidget.php?height=330&width=400";

$script = <<<EOT
	<div id='popup_dialog_$id' class='simplekaltura_popup_dialog'></div>
	<script type='text/javascript'>
	
	$("#popup_dialog_$id").dialog({
						autoOpen: false,
						width: 500, 
						modal: true,
						open: function(event, ui) { 
							console.log('opening');
							$(".ui-dialog-titlebar-close").hide(); 	
						},
						buttons: {
							"X": function() { 
								$(this).dialog("close"); 
							} 
	}});
	
	function simplekaltura_show_popup_$id() {
		$("#popup_dialog_$id").dialog("open");
		$("#popup_dialog_$id").load('$pop_url&entryid=' + '$id');
	}
	
	</script>
EOT;

echo $script . "<img onclick='javascript:simplekaltura_show_popup_$id()' width='153px' src='$thumbnail_url' /><br /><div class='entity_subtext timeline-entity-subtext'>
		Likes: $likes_count $views_string Comments: $comments_count
	</div>". elgg_get_excerpt($vars['entity']->description);
?>