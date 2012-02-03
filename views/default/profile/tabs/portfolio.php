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

/*
$tag_portfolio = $page_owner->tag_portfolio;

if (!$tag_portfolio) {
	$tag_portfolio = "Portfolio";
}
*/

$tag_portfolio = "Portfolio";

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

$title = elgg_view_title(elgg_echo('tagdashboards:label:userportfolio', array($page_owner->name)));

if (elgg_get_logged_in_user_guid() == $page_owner->guid) {
	//$content .= elgg_view_form('tagdashboards/portfolio', array(), array('tag_portfolio' => $tag_portfolio));
} 

if ($page_owner == elgg_get_logged_in_user_entity()) {
	$recommended = elgg_view('tagdashboards/portfolio/recommended', array(
		'user_guid' => $page_owner->guid
	));
}


$content .= <<<HTML
	<div style='float: left;'>
		$title
	</div>
	$recommended
	<div class='clearfix'></div>
	<div class='tagdashboard-container portfolio-left'>
		<div class='tagdashboard-options'>
			$td_type_input
			$td_custom_tags_input
			$td_owner_guids_input
		</div>
		<div class='tagdashboards-content-container no-float'></div>
	</div>
HTML;

echo $content;
