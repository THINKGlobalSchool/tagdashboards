<?php
/**
 * Tag Dashboards portfolio add action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);

$entity = get_entity($guid);

$remove_recommended = get_input('remove_recommended', FALSE);

// Make sure this is a valid object and that we own it
if (!elgg_instanceof($entity, 'object') || elgg_get_logged_in_user_guid() != $entity->getOwnerGUID()) {
	register_error(elgg_echo('tagdashboards:error:invalidentity'));
	forward(REFERER);
}

// Get tags
$tags = $entity->tags;

if (!$tags) { // Empty tags
	$tags = 'portfolio';
	$entity->tags = string_to_tag_array($tags);
	$success = TRUE;
} else if (is_array($tags)) { // Multiple tags
	if (!in_array('portfolio', $tags)) {
		// If not already in tags, add it
		$tags[] = 'portfolio';
		$entity->tags = $tags;
		$success = TRUE;
	}
} else { // One tag
	if ($tags != 'portfolio') {
		// If not already in tags, add it
		$tags .= ', portfolio';
		$entity->tags = string_to_tag_array($tags);
		$success = TRUE;
	}
}

// Remove recommended metadata if set
if ($remove_recommended) {
	tagdashboards_remove_recommended_metadata($entity->guid);
}

if ($success) {
	$entity->save();
	system_message(elgg_echo('tagdashboards:success:addedportfolio'));
}

forward(REFERER);