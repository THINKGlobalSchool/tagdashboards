<?php
/**
 * Timeline view for simplekaltura videos
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

elgg_load_library('simplekaltura');

$video = $vars['entity'];
$guid = $video->guid;

$pop_url = elgg_get_site_url() . 'videos/popup/' . $guid;

$icon = elgg_view_entity_icon($video, 'medium', array(
	'href' => $pop_url,
	'link_class' => 'simplekaltura-lightbox-' . $guid,
	'title' => 'simplekaltura_lightbox',
));

$comments_count = $video->countComments();
$likes_count = likes_count($video);

$width = elgg_get_plugin_setting("kaltura_mediumthumb_width", "simplekaltura");
$height = elgg_get_plugin_setting("kaltura_mediumthumb_height", "simplekaltura");

echo <<<HTML
	<div style='width:{$width}px;height:{$height}px;' onmouseover="javascript:elgg.tagdashboards.timeline.initVideoLightbox('$guid')">$icon</div>
	<br />
	<div class='elgg-subtext timeline-entity-subtext'>
		Likes: $likes_count $views_string Comments: $comments_count
	</div>
HTML;


echo elgg_get_excerpt($video->description);