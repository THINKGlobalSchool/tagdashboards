<?php
/**
 * Tag Dashboards group select form view
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @uses $vars['value']
 */

$offset = elgg_extract('offset', $vars, get_input('offset', 0));
$limit = elgg_extract('limit', $vars, get_input('limit', 10));

$options = array(
	'type' => 'group',
	'limit' => $limit,
	'offset' => $offset,
	'count' => TRUE,
);

$count = elgg_get_entities($options);

unset($options['count']);

$groups = elgg_get_entities($options);

foreach ($groups as $group) {
	$icon = elgg_view_entity_icon($group, 'small');

	$input = "<input type='radio' name='select_group_guid' value='{$group->guid}'/>";

	$group_list .= elgg_view_image_block($input, elgg_get_excerpt($group->name, 50), array('image_alt' => $icon));
}

$nav = elgg_view('navigation/pagination', array(
	'offset' => $offset,
	'count' => $count,
	'limit' => $limit,
	'offset_key' => 'offset',
	'base_url' => elgg_get_site_url() . 'ajax/view/forms/tagdashboards/group_list?limit=' . $limit,
));

echo $group_list;
echo $nav;