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

$title = elgg_view_title(elgg_echo('tagdashboards:label:userportfolio', array($page_owner->name)));

if ($page_owner->guid == elgg_get_logged_in_user_guid()) {
	$recommended = elgg_view('tagdashboards/portfolio/recommended', array(
		'user_guid' => $page_owner->guid
	));
}

$user_input = elgg_view('input/hidden', array(
	'name' => 'portolfio_user_input', 
	'id' => 'portfolio-user', 
	'value' => $page_owner->guid,
));

$content = <<<HTML
	<div style='float: left;'>
		$title
	</div>
	$recommended
	$user_input
	<div id='tagdashboards-portfolio-container'></div>
HTML;

echo $content;
