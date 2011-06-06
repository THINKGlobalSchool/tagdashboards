<?php
/**
 * Tag Dashboards custom bookmark object
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$owner = $vars['entity']->getOwnerEntity();
$friendlytime = elgg_view_friendly_time($vars['entity']->time_created);
$address = $vars['entity']->address;

// you used to be able to add without titles, which created unclickable bookmarks
// putting a fake title in so you can click on it.
if (!$title = $vars['entity']->title) {
	$title = elgg_echo('bookmarks:no_title');
}

$a_tag_visit = filter_tags("<a href=\"{$address}\">" . elgg_echo('bookmarks:visit') . "</a>");
$a_tag_title = filter_tags("<a href=\"{$address}\">$title</a>");


$parsed_url = parse_url($address);
$faviconurl = $parsed_url['scheme'] . "://" . $parsed_url['host'] . "/favicon.ico";

//sort out the access level for display
$object_acl = get_readable_access_level($vars['entity']->access_id);
//files with these access level don't need an icon
$general_access = array('Public', 'Logged in users', 'Friends');
//set the right class for access level display - need it to set on groups and shared access only
$is_group = get_entity($vars['entity']->container_guid);
if($is_group instanceof ElggGroup){
	//get the membership type open/closed
	$membership = $is_group->membership;
	//we decided to show that the item is in a group, rather than its actual access level
	$object_acl = "Group: " . $is_group->name;
	if($membership == 2)
		$access_level = "class='access_level group_open'";
	else
		$access_level = "class='access_level group_closed'";
}elseif($object_acl == 'Private'){
	$access_level = "class='access_level private'";
}else{
	if(!in_array($object_acl, $general_access))
		$access_level = "class='access_level shared_collection'";
	else
		$access_level = "class='access_level entity_access'";
}

if($vars['entity']->description != '')
	$view_notes = "<a class='link' onclick=\"elgg_slide_toggle(this,'.entity_listing','.note');\">note</a>";
else
	$view_notes = '';
if (@file_exists($faviconurl)) {
	$icon = "<img src=\"{$faviconurl}\" />";
} else {
	$icon = elgg_view("profile/icon", array('entity' => $owner,'size' => 'tiny',));
}


$info .= "<p class='entity_title'>$a_tag_title</p>";
$info .= "<p class='entity_subtext'>Bookmarked by <a href=\"".elgg_get_site_url()."bookmarks/{$owner->username}\">{$owner->name}</a> {$friendlytime} {$view_notes}</p>";

$tags = elgg_view('output/tags', array('tags' => $vars['entity']->tags));
if (!empty($tags)) {
	$info .= '<p class="tags">' . $tags . '</p>';
}
if($view_notes != ''){
	$info .= "<div class='note hidden'>". $vars['entity']->description . "</div>";
}

//display
echo elgg_view_listing($icon, $info);
