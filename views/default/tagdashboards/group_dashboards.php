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

$page_owner = elgg_get_page_owner();

if ($page_owner->tagdashboards_enable != "yes") {
	return;
}

//grab the groups bookmarks 
$tagdashboards = elgg_get_entities(array(
	'type' => 'object', 
	'subtype' => 'tagdashboard', 
	'container_guid' => $page_owner->getGUID(), 'limit' => 6
));
?>
<div class="group_tool_widget tagdashboards">
<span class="group_widget_link"><a href="<?php echo elgg_get_site_url() . "pg/tagdashboards/" . elgg_get_page_owner()->username; ?>"><?php echo elgg_echo('link:view:all')?></a></span>
<h3><?php echo elgg_echo('tagdashboards') ?></h3>
<?php	
if($tagdashboards){
	foreach($tagdashboards as $t){
			
		//get the owner
		$owner = $t->getOwnerEntity();

		//get the time
		$friendlytime = elgg_view_friendly_time($t->time_created);
		
	    $info = "<div class='entity_listing_icon'>" . elgg_view('profile/icon',array('entity' => $t->getOwnerEntity(), 'size' => 'tiny')) . "</div>";

		//get the blog entries body
		$info .= "<div class='entity_listing_info'><p class='entity_title'><a href=\"{$t->getURL()}\">{$t->title}</a></p>";
				
		//get the user details
		$info .= "<p class='entity_subtext'>{$friendlytime}</p>";
		$info .= "</div>";
		//display 
		echo "<div class='entity_listing clearfix'>" . $info . "</div>";
	} 
} else {
	$add = elgg_get_site_url() . "pg/tagdashboards/add/" . elgg_get_page_owner()->username;
	echo "<p class='margin_top'><a href=\"{$add}\">" . elgg_echo("tagdashboard:new") . "</a></p>";
}
echo "</div>";