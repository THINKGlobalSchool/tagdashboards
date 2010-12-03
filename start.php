<?php
/**
 * Ubertags start.pjp
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

function ubertags_init() {
	global $CONFIG;
	
	// Include helpers
	require_once 'lib/ubertags_lib.php';
			
	// Extend CSS
	elgg_extend_view('css','ubertags/css');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'ubertags/admin/css');
	
	// Page handler
	register_page_handler('ubertags','ubertags_page_handler');

	// Add to tools menu
	add_menu(elgg_echo("ubertags"), $CONFIG->wwwroot . 'pg/ubertags/search');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','ubertags_submenus');
					
	// Set up url handlers
	elgg_register_event_handler('ubertag_url','object', 'ubertag');

	// Register actions
	register_action('ubertags/create', false, $CONFIG->pluginspath . 'ubertags/actions/create.php');
	register_action('ubertags/admin_enable_subtypes', false, $CONFIG->pluginspath . 'ubertags/actions/admin_enable_subtypes.php');
	
	// Test.. for exceptions
	elgg_register_plugin_hook_handler('ubertags', 'exceptions', 'testhook');
	elgg_register_plugin_hook_handler('ubertags:subtype', 'image', 'testhook2');

	// Register type
	register_entity_type('object', 'ubertag');		

	return true;
	
}

/* Test for exceptions */
function testhook($hook, $type, $returnvalue, $params) {
	unset($returnvalue[array_search('plugin', $returnvalue)]);
	$returnvalue[] = 'todo';
	return $returnvalue;
}

/* Test for subtypes */
function testhook2($hook, $type, $returnvalue, $params) {
	return "Test";
}

/* Ubertags page handler */
function ubertags_page_handler($page) {
	global $CONFIG;
	set_context('ubertags');
	gatekeeper();
	switch ($page[0]) {
		case 'search':
			$content_info = ubertags_get_page_content_search();
		break;
		case 'settings':
			admin_gatekeeper();
			set_context('admin');
			$content_info = ubertags_get_page_content_admin_settings();
		break;
	}
	
	$sidebar = isset($content_info['sidebar']) ? $content_info['sidebar'] : '';

	$params = array(
		'content' => elgg_view('navigation/breadcrumbs') . $content_info['content'],
		'sidebar' => $sidebar,
	);
	$body = elgg_view_layout($content_info['layout'], $params);

	echo elgg_view_page($content_info['title'], $body, $content_info['layout'] == 'administration' ? 'page_shells/admin' : 'page_shells/default');
}
	
/**
 * Setup ubertags submenus
 */
function ubertags_submenus() {
	global $CONFIG;
	$page_owner = elgg_get_page_owner();
	
	// Admin 
	if (isadminloggedin()) {
		elgg_add_submenu_item(array('text' => elgg_echo('ubertags:title:adminsettings'), 
									'href' => $CONFIG->url . "pg/ubertags/settings"), 'admin', 'z');
	}
}
	
/**
 * Populates the ->getUrl() method for an ubertag
 *
 * @param ElggEntity entity
 * @return string request url
 */
function ubertag_url($entity) {
	global $CONFIG;
	return $CONFIG->url . "pg/ubertags/view/{$entity->guid}/";
}

register_elgg_event_handler('init', 'system', 'ubertags_init');
?>