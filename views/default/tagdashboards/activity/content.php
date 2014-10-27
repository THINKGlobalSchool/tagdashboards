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

echo elgg_view('filtrate/dashboard', array(
	'menu_name' => '',
	'infinite_scroll' => true,
	'default_params' => array(
		'type' => 0
	),
	'list_url' => elgg_get_site_url() . 'ajax/view/tagdashboards/activity/list?dashboard_guid=' . $dashboard_guid,
	'id' => 'activity-filtrate'
));