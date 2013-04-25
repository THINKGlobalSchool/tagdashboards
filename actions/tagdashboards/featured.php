<?php
/**
 * Tag Dashboards feature action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');
$action = get_input('action_type');

$dashboard = get_entity($guid);

if (!elgg_instanceof($dashboard, 'object', 'tagdashboard')) {
	register_error(elgg_echo('tagdashboards:error:featured'));
	forward(REFERER);
}

//get the action, is it to feature or unfeature
if ($action == "feature") {
	$dashboard->featured_dashboard = "yes";
	system_message(elgg_echo('tagdashboards:success:featuredon', array($dashboard->title)));
} else {
	$dashboard->featured_dashboard = "no";
	system_message(elgg_echo('tagdashboards:success:featuredoff', array($dashboard->title)));
}

forward(REFERER);
