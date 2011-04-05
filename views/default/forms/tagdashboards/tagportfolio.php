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

// Register autocomplete JS
$auto_url = elgg_get_site_url() . "vendors/jquery/jquery.autocomplete.min.js";
elgg_register_js($auto_url, 'jquery.autocomplete');

$instructions = elgg_echo('tagdashboards:description:tagportfolio');

// Tags search input
$custom_input = elgg_view('forms/tagdashboards/custom_tags', array('value' => $tag_portfolio));

// Refresh button
$refresh_input = elgg_view('input/submit', array(
	'internalid' => 'tagdashboards-refresh-input',
	'internalname' => 'tagdashboards_refresh_input',
	'value' => elgg_echo('tagdashboards:label:refresh')
));

// Refresh button
$save_input = elgg_view('input/submit', array(
	'internalid' => 'tagdashboards-save-input',
	'internalname' => 'tagdashboards_save_input',
	'value' => elgg_echo('tagdashboards:label:save')
));

$action = 'action/tagdashboards/save_tag_portfolio';

$form_body = <<<HTML
	<br />
	<p>$instructions</p>
	<form action=''>
	$custom_input
	$refresh_input $save_input
HTML;

echo elgg_view('input/form', array(
	'internalname' => 'tagdashboards-portfolio-save-form',
	'internalid' => 'tagdashboards-portfolio-save-form',
	'body' => $form_body,
	'action' => $action
));