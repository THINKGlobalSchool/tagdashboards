<?php
/**
 * Tag Dashboards Recommended Items 
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = get_entity(elgg_extract('user_guid', $vars, elgg_get_logged_in_user_guid()));

if (elgg_instanceof($user, 'user')) {
	$title = elgg_echo('tagdashboards:label:recommended');

	// Count initial recommended items
	$options = array(
		'owner_guid' => $user->guid,
		'metadata_name_value_pairs' => array(array(
			'name' => 'recommended_portfolio', 
			'value' => '1', 
			'operand' => '=',
			'case_sensitive' => FALSE
		)),
		'count' => TRUE,
	);

	$count = elgg_get_entities_from_metadata($options);
	
	$count_content = "<span id='portfolio-recommended-count'>$count</span>";
	
	// Create group module				
	$module = elgg_view('modules/genericmodule', array(
		'view' => 'tagdashboards/module/recommended',
		'module_id' => 'tagdashboard-portfolio-recommended',
		'view_vars' => array('user_guid' => $user->guid), 
	));

	echo elgg_view('output/url', array(
		'href' => '#tagdashboards-recommended-dropdown',
		'rel' => 'popup',
		'class' => 'elgg-button elgg-button-action tagdashboards-recommended-button',
		'text' => "<span class='portfolio-recommended-text'>" . elgg_echo('tagdashboards:label:showrecommended', array($count_content)) . "</span><span class='portfolio-recommended-caret'>▾</span>",
	));

	echo elgg_view_module('dropdown', $title, $module, array(
		'class' => 'tagdashboard-module',
		'id' => 'tagdashboards-recommended-dropdown',
	));
}