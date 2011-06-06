<?php
/**
 * Tag Dashboards delete action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);
$tagdashboard = get_entity($guid);

if (elgg_instanceof($tagdashboard, 'object', 'tagdashboard') && $tagdashboard->canEdit()) {
	$container = get_entity($tagdashboard->container_guid);
	if ($tagdashboard->delete()) {
		system_message(elgg_echo('tagdashboards:success:delete'));
		forward("tagdashboards/{$container->username}");
	} else {
		register_error(elgg_echo('tagdashboards:error:delete'));
	}
} else {
	register_error(elgg_echo('tagdashboards:error:notfound'));
}

forward(REFERER);