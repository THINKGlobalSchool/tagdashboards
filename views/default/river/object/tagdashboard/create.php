<?php
/**
 * Tag Dashboards create tagdashboard river item
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */


$performed_by = get_entity($vars['item']->subject_guid);
$object = get_entity($vars['item']->object_guid);
$url = $object->getURL();
$contents = strip_tags($object->description);

$user_url = "<a href=\"{$performed_by->getURL()}\">{$performed_by->name}</a>";
$title = elgg_echo('tagdashboards:river:tagdashboard:create', array($user_url));
$string .= $title . " <a href=\"{$object->getURL()}\">{$object->title}</a> <span class='entity_subtext'>" . elgg_view_friendly_time($object->time_created);

if (isloggedin()) {
	$string .= '<a class="river_comment_form_button link">' . elgg_echo('generic_comments:text') . '</a>';
	$string .= elgg_view('likes/forms/link', array('entity' => $object));
}
$string .= "</span>";

$string .= '<div class="river_content_display">';
$string .= '<div class="river_object_blog_create"></div>';
if (strlen($contents) > 200) {
		$string .= substr($contents, 0, strpos($contents, ' ', 200)) . '&hellip;';
} else {
	$string .= $contents;
}
$string .= '</div>';
echo $string;