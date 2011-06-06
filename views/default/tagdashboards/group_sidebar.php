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

$group = page_owner_entity();
$view_by_activity_url = elgg_get_site_url() . 'tagdashboards/group_activity/' . $group->getGUID(); 

?>
<div class="group_tool_widget" style='height: auto; margin-bottom: 5px; min-height: 100%;'>
	<h3><?php echo elgg_echo('tagdashboard') ?></h3>
	<a href='<?php echo $view_by_activity_url; ?>'><?php echo elgg_echo('tagdashboards:label:groupbyactivity'); ?></a>
</div>

