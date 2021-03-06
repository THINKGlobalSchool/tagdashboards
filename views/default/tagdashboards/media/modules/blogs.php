<?php
/**
 * Tag Dashboards Blogs Module View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 * @uses $vars['dashboard_guid']
 */

$dashboard_guid = elgg_extract('dashboard_guid', $vars);

$options = tagdashboards_get_media_entities_options($dashboard_guid, array(
	'subtype' => 'blog',
	'limit' => 5,
	'full_view' => FALSE,
));

set_input('content_teaser_view', 1);

$content = elgg_list_entities_from_metadata($options);

if (!$content) {
	$content = "<div style='width: 100%; text-align: center; margin: 10px;'><strong>No results</strong></div>";
}

echo $content;