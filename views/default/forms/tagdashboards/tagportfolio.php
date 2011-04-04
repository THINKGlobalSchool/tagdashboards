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

$page_owner = elgg_get_page_owner();
$tag_portfolio = $page_owner->tag_portfolio;


// Register tag dashboards JS library
$url = elgg_view_get_simplecache_url('js', 'tagdashboards');
elgg_register_js($url, 'tagdashboards');

// Register autocomplete JS
$auto_url = elgg_get_site_url() . "vendors/jquery/jquery.autocomplete.min.js";
elgg_register_js($auto_url, 'jquery.autocomplete');

$script = <<<HTML
	<script type='text/javascript'>
	// Hack to switch the selected tab to the proper one..won't need this in 1.8
	$('div.profile > ul > li').removeClass('selected');
	$('li#tagportfolio-tab').addClass('selected');
	
	$(document).ready(function() {
		
		// Set up options
		var options = new Array();
		options['type'] = 'custom';
		options['custom_tags'] = $('#tagdashboards-custom-input').val();
		
		elgg.tagdashboards.display(options);
		
		$('#tagdashboards-refresh-input').click(function() {
			options['custom_tags'] = $('#tagdashboards-custom-input').val();
			
			elgg.tagdashboards.display(options);
			return false;
		});
	});
</script>
HTML;

$header = elgg_view_title(elgg_echo('tagdashboards:title:tagportfolio'));
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
	$script
	$header
	<br />
	<p>$instructions</p>
	<form action=''>
	$custom_input
	<br />
	$refresh_input $save_input
	<div id='tagdashboards-content-container' class='tagdashboards-left'>
	</div>
HTML;

echo elgg_view('input/form', array(
	'internalname' => 'tagdashboards-portfolio-save-form',
	'internalid' => 'tagdashboards-portfolio-save-form',
	'body' => $form_body,
	'action' => $action
));