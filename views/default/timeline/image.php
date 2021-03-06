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
$views = elgg_get_annotations(array(
	'guid' => $vars['entity']->getGUID(), 
	'type' => "object", 
	'subtype' => "image", 
	'annotation_names' => array("tp_view"),
	'count' => TRUE,
));

if ($views) {
	$views_string = sprintf(elgg_echo("tidypics:views"), $views);
}

$image_link = elgg_get_site_url() . "photos/thumbnail/{$vars['entity']->getGUID()}/small";

$comments_count = $vars['entity']->countComments();
$likes_count = likes_count($vars['entity']);

$src = elgg_get_site_url() . "photos/thumbnail/{$vars['entity']->getGUID()}/large";

echo "<a href='$src/image.jpeg' class='modules-lightbox'><img src='$image_link' /></a><br />
		<div class='elgg-subtext timeline-entity-subtext'>
			Likes: $likes_count $views_string Comments: $comments_count
		</div>"
		.  elgg_get_excerpt($vars['entity']->description);
