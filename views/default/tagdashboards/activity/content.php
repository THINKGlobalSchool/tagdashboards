<?php
/**
 * Tag Dashboards Activity Content View
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

elgg_register_plugin_hook_handler('view', 'river/elements/layout', 'spiffyactivity_river_layout_view_handler');

echo elgg_view('modules/genericmodule', array(
	'view' => 'tagdashboards/activity/list',
	'view_vars' => array('dashboard_guid' => $dashboard_guid),
));