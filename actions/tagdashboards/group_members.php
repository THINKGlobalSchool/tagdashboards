<?php
/**
 * Tag Dashboards get group members action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$group_guid = get_input('group_guid');

$group = get_entity($group_guid);

if (!elgg_instanceof($group, 'group')) {
	register_error(elgg_echo('tagdashboards:error:invalidgroup'));
	forward(REFERER);
}

$members = $group->getMembers(0);

$members_info = array();

foreach ($members as $member) {
	$output = elgg_view_list_item($member, array(
		'use_hover' => false,
		'class' => 'elgg-autocomplete-item',
	));

	$icon = elgg_view_entity_icon($member, 'tiny', array(
		'use_hover' => false,
	));

	$info = array(
		'type' => 'user',
		'name' => $member->name,
		'desc' => $member->username,
		'guid' => $member->guid,
		'label' => $output,
		'value' => $member->username,
		'icon' => $icon,
		'url' => $member->getURL(),
	);

	$members_info[] = $info;
}

echo json_encode($members_info);

forward(REFERER);