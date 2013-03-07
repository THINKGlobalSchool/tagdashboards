<?php
/**
 * Tag Dashboards Media Content View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['dashboard_guid']
 */

$dashboard_guid = elgg_extract('dashboard_guid', $vars);

$blogs = elgg_view('tagdashboards/media/blogs', $vars);
$images = elgg_view('tagdashboards/media/albums', $vars);
$videos = elgg_view('tagdashboards/media/videos', $vars);

$content = <<<HTML
	<div class='tagdashboards-media-content-container'>
		$videos
		$blogs
		$images
	</div>
HTML;

echo $content;