<?php
/**
 * Tag Dashboards Albums Media View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['dashboard_guid']
 */

// Create ajax module				
$albums = elgg_view('modules/genericmodule', array(
	'view' => 'tagdashboards/media/modules/albums',
	'view_vars' => $vars,
));

echo elgg_view_module('info', 'Albums', $albums, array('class' => 'tagdashboards-media-images-container'));