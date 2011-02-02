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

echo  "<img width='153px' src='$thumbnail_url' /><br /><div class='entity_subtext timeline-entity-subtext'>
		Likes: $likes_count $views_string Comments: $comments_count
	</div>". elgg_get_excerpt($vars['entity']->description);
?>