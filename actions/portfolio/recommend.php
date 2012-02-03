<?php
/**
 * Tag Dashboards portfolio recommend action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);
$remove = get_input('remove', FALSE);

$entity = get_entity($guid);

// Make sure this is a valid object and that we own it
if (!elgg_instanceof($entity, 'object')) {
	register_error(elgg_echo('tagdashboards:error:invalidentity'));
	forward(REFERER);
}

// Ignore access to allow any user to add the recommended metadata
$ia = elgg_get_ignore_access();
elgg_set_ignore_access(TRUE);
if (!$remove) {
	if (!$entity->recommended_portfolio) {
		$entity->recommended_portfolio = 1;
		$success = $entity->save();
	}
} else {
	// Remove the recommended metadata
	tagdashboards_remove_recommended_metadata($entity->guid);
}
elgg_set_ignore_access($ia);
if ($success) {
	system_message(elgg_echo('tagdashboards:success:recommendportfolio', array($entity->getOwnerEntity()->name)));
}
forward(REFERER);