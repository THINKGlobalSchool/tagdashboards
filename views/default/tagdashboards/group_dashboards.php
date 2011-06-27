<?php
/**
 * Tag Dashboards group module
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$group = elgg_get_page_owner_entity();

if ($group->tagdashboards_enable == "no") {
	return true;
}

$all_link = elgg_view('output/url', array(
	'href' => "tagdashboards/group/$group->guid/owner",
	'text' => elgg_echo('link:view:all'),
));

elgg_push_context('widgets');
$options = array(
	'type' => 'object',
	'subtype' => 'tagdashboard',
	'container_guid' => elgg_get_page_owner_guid(),
	'limit' => 6,
	'full_view' => false,
	'pagination' => false,
);
$content = elgg_list_entities($options);
elgg_pop_context();

if (!$content) {
	$content = '<p>' . elgg_echo('tagdashboards:none') . '</p>';
}

$new_link = elgg_view('output/url', array(
	'href' => "tagdashboards/add/$group->guid",
	'text' => elgg_echo('tagdashboards:add'),
));

echo elgg_view('groups/profile/module', array(
	'title' => elgg_echo('tagdashboards:label:grouptags'),
	'content' => $content,
	'all_link' => $all_link,
	'add_link' => $new_link,
));

