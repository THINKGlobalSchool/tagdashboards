<?php
/**
 * Tag Dashboards portfolio remove action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);

$entity = get_entity($guid);

// Make sure this is a valid object and that we own it
if (!elgg_instanceof($entity, 'object') || elgg_get_logged_in_user_guid() != $entity->getOwnerGUID()) {
	register_error(elgg_echo('tagdashboards:error:invalidentity'));
	forward(REFERER);
}

// Get tags
$tags = $entity->tags;

if (is_array($tags)) { // Multiple tags
	if (in_array('portfolio', $tags)) {		
		if (($key = array_search('portfolio', $tags)) !== false) {
			unset($tags[$key]); // Unset portfolio tags
		}

		$entity->tags = $tags;
		$success = TRUE;
	}
} else { // One tag
	if ($tags == 'portfolio') {
		$entity->tags = null; // clear out tags (portfolio was the only tag)
		$success = TRUE;
	}
}

if ($success) {
	$entity->save();
	system_message(elgg_echo('tagdashboards:success:removedportfolio'));
}

forward(REFERER);