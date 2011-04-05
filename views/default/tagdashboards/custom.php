<?php
/**
 * Tag Dashboards group by user defined, based on search tag container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$custom = $vars['custom_tags'];
$search = $vars['search'];
$subtypes = $vars['subtypes'];
$owner_guids = $vars['owner_guids'];

foreach($custom as $tag) {
	$content .= elgg_view('tagdashboards/custom_container', array(
		'search' => $search, 
		'group' => $tag, 
		'subtypes' => $subtypes,
		'owner_guids' => $owner_guids,
	));
}

echo $content . "<div style='clear: both;'></div>";