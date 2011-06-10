<?php
/**
 * Tag Dashboards tag porfolio form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$tag_portfolio = $vars['tag_portfolio'];

$instructions = elgg_echo('tagdashboards:description:tagportfolio');

// Tags search input
$custom_input = elgg_view('forms/tagdashboards/custom_tags', array('value' => $tag_portfolio));

$td_owner_guids_input = elgg_view('input/hidden', array(
	'name' => 'owner_guids[]', 
	'id' => 'owner_guids', 
	'value' => elgg_get_page_owner_guid(),
));

// Refresh button
$refresh_input = elgg_view('input/submit', array(
	'id' => 'tagdashboards-refresh-input',
	'name' => 'tagdashboards_refresh_input',
	'value' => elgg_echo('tagdashboards:label:refresh')
));

// Refresh button
$save_input = elgg_view('input/submit', array(
	'id' => 'tagdashboards-save-input',
	'name' => 'tagdashboards_save_input',
	'value' => elgg_echo('tagdashboards:label:save')
));

$form_body = <<<HTML
	<br />
	<p>$instructions</p>
	$custom_input
	$refresh_input $save_input
	$td_owner_guids_input
	<input type='checkbox' style='display: none;' id='tagdashboard-groupby-input' value='custom' checked />
HTML;

echo $form_body;