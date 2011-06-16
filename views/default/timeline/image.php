<?php
/**
 * Timeline view for images
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Description will contain the img link, likes, views, comments and excerpt
$views = count_annotations($vars['entity']->getGUID(), "object", "image", "tp_view");

if ($views) {
	$views_string = sprintf(elgg_echo("tidypics:views"), $views);
}

$image_link = elgg_get_site_url() . "photos/thumbnail/{$vars['entity']->getGUID()}/small";

$comments_count = elgg_count_comments($vars['entity']);
$likes_count = likes_count($vars['entity']);

$id = $vars['entity']->getGUID();
$src = elgg_get_site_url() . "photos/thumbnail/{$vars['entity']->getGUID()}/large";

echo "<div class='timeline-tidypics-image-container'>
		<div style='display: none;' id='popup-dialog-$id' class='image-popup-dialog'></div>
		<a href='$src/image.jpeg' class='timeline-lightbox'><img src='$image_link' /></a><br />
		<div class='elgg-subtext timeline-entity-subtext'>
			Likes: $likes_count $views_string Comments: $comments_count
		</div>"
		.  elgg_get_excerpt($vars['entity']->description) . 
	"</div>";
