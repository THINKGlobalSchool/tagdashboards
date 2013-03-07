<?php
/**
 * Tag Dashboards Video Popup Extension
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['entity']
 */

$video = elgg_extract('entity', $vars);

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

