<?php
/**
 * Ubertags content container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

switch ($vars['group_by']) {
	case UBERTAGS_GROUP_ACTIVITY:
		$activities = ubertags_get_group_activities();
		$group_guid = $vars['container_guid'];
		foreach($activities as $activity) {
			$results .= elgg_view('ubertags/activity_container', array(
				'group' => $group_guid, 
				'activity' => $activity, 
			));
		}
	break;
	case UBERTAGS_GROUP_ACTIVITY_TAGS:
		$results = "Not implemented";
	break;
	case UBERTAGS_GROUP_TAGS:
		$results = "Not implemented";
	break;
	default:
	case UBERTAGS_GROUP_SUBTYPE:
		// Grab subtypes if they were supplied
		$subtypes = unserialize($vars['subtypes']);

		// If we weren't supplied an array of subtypes, use defaults
		if (!is_array($subtypes)) {
			$subtypes = ubertags_get_enabled_subtypes();
		}
		
		// Loop over and display each
		foreach ($subtypes as $subtype) {
			$results .= elgg_view('ubertags/subtype_container', array(
				'subtype' => $subtype, 
				'search' => $vars['search']
			));
		}
	break;
}

echo $results . "<div style='clear: both;'></div>";
