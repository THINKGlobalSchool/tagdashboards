<?php
/**
 * Tag Dashboards view by activity sidebar
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$group = elgg_get_page_owner_entity();

// Only display sidebar if tagdashboards are enabled
if (elgg_instanceof($group, 'group') && $group->tagdashboards_enable == 'yes') {
	$view_by_activity_url = elgg_get_site_url() . 'tagdashboards/group_activity/' . $group->getGUID(); 
	$content = "<a href='$view_by_activity_url'>" . elgg_echo('tagdashboards:label:groupbyactivity') . "</a>";	
	echo elgg_view_module('aside', elgg_echo('tagdashboards'), $content);
}

