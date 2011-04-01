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

// Register tag dashboards JS library
$url = elgg_view_get_simplecache_url('js', 'tagdashboards');
elgg_register_js($url, 'tagdashboards');

// Register autocomplete JS
$auto_url = elgg_get_site_url() . "vendors/jquery/jquery.autocomplete.min.js";
elgg_register_js($auto_url, 'jquery.autocomplete');

$script = "<script type='text/javascript'>
	// Hack to switch the selected tab to the proper one..won't need this in 1.8
	$('div.profile > ul > li').removeClass('selected');
	$('li#tagportfolio-tab').addClass('selected');
	
	$(document).ready(function() {
		$('#tagdashboards-refresh-input').click(function() {
			elgg.tagdashboards.submit_search('', 'custom', null, true);
		});
	});
</script>";

$header = elgg_view_title(elgg_echo('tagdashboards:title:tagportfolio'));
$instructions = elgg_echo('tagdashboards:description:tagportfolio');

// Tags search input
$custom_input = elgg_view('forms/tagdashboards/custom_tags');

// Refresh button
$refresh_input = elgg_view('input/submit', array(
	'internalid' => 'tagdashboards-refresh-input',
	'internalname' => 'tagdashboards_refresh_input',
	'value' => elgg_echo('tagdashboards:label:refresh')
));

echo <<<HTML
	$script
	$header
	<br />
	<p>$instructions</p>
	$custom_input
	<span id='tagdashboards-search-error'></span><br />
	$refresh_input
	<div id='tagdashboards-content-container'>
	</div>
HTML;
?>