<?php
/**
 * Tag Dashboards profile tab
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Load JS/CSS
elgg_load_js('elgg.tagdashboards');
elgg_load_css('elgg.tagdashboards');

$page_owner = elgg_get_page_owner_entity();

$tag_portfolio = $page_owner->tag_portfolio;

if (!$tag_portfolio) {
	$tag_portfolio = "Portfolio";
}

// Tag Dashboard Content inputs
$td_type_input = elgg_view('input/hidden', array(
	'name' => 'type', 
	'id' => 'type', 
	'value' => 'custom',
));

$td_custom_tags_input = elgg_view('input/hidden', array(
	'name' => 'custom_tags', 
	'id' => 'custom_tags', 
	'value' => json_encode($tag_portfolio)
));

$td_owner_guids_input = elgg_view('input/hidden', array(
	'name' => 'owner_guids', 
	'id' => 'owner_guids', 
	'value' => json_encode(array($page_owner->guid))
));

$content = elgg_view_title(elgg_echo('profile:tagportfolio'));

if (elgg_get_logged_in_user_guid() == $page_owner->guid) {
	$content .= elgg_view_form('tagdashboards/tagportfolio', array(), array('tag_portfolio' => $tag_portfolio));
} 

$content .= <<<HTML
	<br />
	<div class='tagdashboard-container'>
		<div class='tagdashboard-options'>
			$td_type_input
			$td_custom_tags_input
			$td_owner_guids_input
		</div>
		<div class='tagdashboards-content-container'></div>
	</div>
HTML;

echo $content;
