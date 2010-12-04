<?php
/**
 * Ubertags start.php
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * 
 * /////////// @TODO ///////////////
 * - River entries
 * - Select subtypes before saving
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
	add_menu(elgg_echo("ubertags"), $CONFIG->wwwroot . 'pg/ubertags');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','ubertags_submenus');
					
	// Set up url handlers
	elgg_register_event_handler('ubertag_url','object', 'ubertag');

	// Register actions
	register_action('ubertags/save', false, $CONFIG->pluginspath . 'ubertags/actions/save.php');
	register_action('ubertags/delete', false, $CONFIG->pluginspath . 'ubertags/actions/delete.php');
	register_action('ubertags/admin_enable_subtypes', false, $CONFIG->pluginspath . 'ubertags/actions/admin_enable_subtypes.php');
	
	// Setup url handler for ubertags
	register_entity_url_handler('ubertags_url_handler','object', 'ubertag');
	
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'ubertag_annotate_comments');
	
	// Test.. for exceptions
	//elgg_register_plugin_hook_handler('ubertags', 'exceptions', 'testhook');
	//elgg_register_plugin_hook_handler('ubertags:subtype', 'image', 'testhook2');

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

	if (isset($page[0]) && !empty($page[0])) {
		switch ($page[0]) {
			case 'friends': 
				$content_info = ubertags_get_page_content_friends(get_loggedin_userid());
			break;
			case 'search':
				$content_info = ubertags_get_page_content_search();
			break;
			case 'settings':
				admin_gatekeeper();
				set_context('admin');
				$content_info = ubertags_get_page_content_admin_settings();
			break;
			case 'view': 
				$content_info = ubertags_get_page_content_view($page[1]);
			break;
			default:
				// Should be a username if we're here
				if (isset($page[0])) {
					$owner_name = $page[0];
					set_input('username', $owner_name);
				} else {
					set_page_owner(get_loggedin_userid());
				}
				// grab the page owner
				$owner = elgg_get_page_owner();
				$content_info = ubertags_get_page_content_list($owner->getGUID());
			break;
		}
	} else {
		$content_info = ubertags_get_page_content_list();
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
function ubertags_url_handler($entity) {
	global $CONFIG;
	return $CONFIG->url . "pg/ubertags/view/{$entity->guid}/";
}

/**
 * Hook into the framework and provide comments on ubertags
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function ubertag_annotate_comments($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$full = $params['full'];
	
	if (
		($entity instanceof ElggEntity) &&	// Is the right type 
		($entity->getSubtype() == 'ubertag') &&  // Is the right subtype
		($full) // This is the full view
	)
	{
		// Display comments
		return elgg_view_comments($entity);
	}
	
}

register_elgg_event_handler('init', 'system', 'ubertags_init');
?>