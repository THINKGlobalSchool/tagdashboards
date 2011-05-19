<?php
/**
 * Tag Dashboards custom discussion object view
 * YIKES. This is a mess.. Thankfully not in 1.8
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

//get the required variables
$title = htmlentities($vars['entity']->title, ENT_QUOTES, 'UTF-8');
//$description = get_entity($vars['entity']->description);
$topic_owner = get_user($vars['entity']->owner_guid);
$group = get_entity($vars['entity']->container_guid);
$forum_created = elgg_view_friendly_time($vars['entity']->time_created);
$counter = $vars['entity']->countAnnotations("generic_comment");
$last_post = $vars['entity']->getAnnotations("generic_comment", 1, 0, "desc");
//get the time and user
if ($last_post) {
	foreach($last_post as $last) {
		$last_time = $last->time_created;
		$last_user = $last->owner_guid;
	}
}

$u = get_user($last_user);


$info .= "<p class='entity_title'><a href=\"".elgg_get_site_url()."mod/groups/topicposts.php?topic={$vars['entity']->guid}&group_guid={$group->guid}\">{$title}</a></p>";

if (($last_time) && ($u)) {
	$commenter_link = "<a href\"{$u->getURL()}\">$u->name</a>";
	$text = elgg_echo('groups:lastcomment', array(elgg_view_friendly_time($last_time), $commenter_link));
}

if($counter == 1){
	$info .= "<p class='entity_subtext'>$text - $counter reply</p>";
}else{
	$info .= "<p class='entity_subtext'>$text - $counter replies</p>";
}

//get the user avatar
$icon = elgg_view("profile/icon",array('entity' => $topic_owner, 'size' => 'tiny'));


//display
echo elgg_view_listing($icon, $info);