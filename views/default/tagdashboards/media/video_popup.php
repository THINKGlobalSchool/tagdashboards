<?php
/**
 * Tag Dashboards Video Popup Extension
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 *
 * @uses $vars['entity']
 */

if ($vars['entity']) {
	$video = $vars['entity'];
} else {
	$video = get_entity($vars['entity_guid']);	
}

if (!elgg_instanceof($video, 'object')) {
	return false;
}

echo elgg_view('output/url', array(
	'text' => elgg_echo('tagdashboards:label:viewvideo'),
	'href' => $video->getURL(),
	'target' => '_blank',
	'class' => 'ptm',
	'style' => 'display: block',
));

echo elgg_view('output/longtext', array(
	'value' => $video->description,
	'class' => 'pts',
	'style' => 'width: 600px',
));

