<?php
/**
 * Tag Dashboards add generic tag action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);

$tag = get_input('tag', null);

$ignore_access = get_input('ia', FALSE);

$entity = get_entity($guid);

// Make sure this is a valid object
if (!elgg_instanceof($entity, 'object')) {
	register_error(elgg_echo('tagdashboards:error:invalidentity'));
	forward(REFERER);
}

// Make sure we were supplied a tag string
if ($tag == NULL || !is_string((string)$tag)) {
	register_error(elgg_echo('tagdashboards:error:invalidtag'));
	forward(REFERER);
}

// Get tags
$tags = $entity->tags;

// Ignore access to allow any user to add the recommended metadata
$ia = elgg_get_ignore_access();
if ($ignore_access) { // Optional
	elgg_set_ignore_access(TRUE);
}

if (!$tags) { // Empty tags
	$tags = $tag;
	$entity->tags = string_to_tag_array($tags);
	$success = TRUE;
} else if (is_array($tags)) { // Multiple tags
	if (!in_array($tag, $tags)) {
		// If not already in tags, add it
		$tags[] = $tag;
		$entity->tags = $tags;
		$success = TRUE;
	}
} else { // One tag
	if ($tags != $tag) {
		// If not already in tags, add it
		$tags .= ", $tag";
		$entity->tags = string_to_tag_array($tags);
		$success = TRUE;
	}
}

elgg_set_ignore_access($ia);

if ($success) {
	system_message(elgg_echo('tagdashboards:success:addtag', array($tag)));
}
forward(REFERER);