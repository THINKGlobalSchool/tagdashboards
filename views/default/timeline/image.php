<?php
/**
 * Timeline view for images
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */


// Description will contain the img link, likes, views, comments and excerpt
$views = $vars['entity']->getViews(0);
if (is_array($views)) {
	$views_string = sprintf(elgg_echo("tidypics:views"), $views['total']);
}

$image_link = elgg_get_site_url() . "pg/photos/thumbnail/{$vars['entity']->getGUID()}/small";

$comments_count = elgg_count_comments($vars['entity']);
$likes_count = elgg_count_likes($vars['entity']);

echo "<div class='timeline-tidypics-image-container'>
		<img src='$image_link' /><br />
		<div class='entity_subtext timeline-entity-subtext'>
			Likes: $likes_count $views_string Comments: $comments_count
		</div>"
		.  elgg_get_excerpt($vars['entity']->description) . 
	"</div>";
?>