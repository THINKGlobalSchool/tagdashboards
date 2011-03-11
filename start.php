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
 */

elgg_register_event_handler('init', 'system', 'ubertags_init');

function ubertags_init() {
	global $CONFIG;
	
	// Include helpers
	require_once 'lib/ubertags_lib.php';
	require_once 'lib/ubertags_hooks.php';
	
	define('UBERTAGS_GROUP_ACTIVITY', 'activity');
	define('UBERTAGS_GROUP_ACTIVITY_TAGS', 'activity_tags');	
	define('UBERTAGS_GROUP_SUBTYPE', 'subtype');
	define('UBERTAGS_GROUP_TAGS', 'tags');
	
	// Extend CSS
	elgg_extend_view('css/screen','ubertags/css');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'ubertags/admin/css');
	
	// Add ubertag activity to sidebar
	elgg_extend_view('group-extender/sidebar','ubertags/group_sidebar', 0);
	
	// Page handler
	register_page_handler('ubertags','ubertags_page_handler');

	// Add to tools menu
	add_menu(elgg_echo("ubertags"), $CONFIG->wwwroot . 'pg/ubertags');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','ubertags_submenus');
					
	// Set up url handlers
	elgg_register_event_handler('ubertag_url','object', 'ubertag');

	// Register actions
	elgg_register_action('ubertags/save', $CONFIG->pluginspath . 'ubertags/actions/save.php');
	elgg_register_action('ubertags/edit', $CONFIG->pluginspath . 'ubertags/actions/edit.php');
	elgg_register_action('ubertags/delete', $CONFIG->pluginspath . 'ubertags/actions/delete.php');
	elgg_register_action('ubertags/admin_enable_subtypes', $CONFIG->pluginspath . 'ubertags/actions/admin_enable_subtypes.php', 'admin');
	
	// Setup url handler for ubertags
	register_entity_url_handler('ubertags_url_handler','object', 'ubertag');
	
	// Comment handler
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'ubertag_annotate_comments');
	
	// Provide the jquery resize plugin
	elgg_register_js(elgg_get_site_url() . 'mod/ubertags/vendors/jquery.resize.js', 'jquery.resize');
	
	//elgg_register_plugin_hook_handler('ubertags', 'exceptions', 'ubertags_exception_example');
	//elgg_register_plugin_hook_handler('ubertags:subtype', 'image', 'ubertags_subtype_example');
	
	// Change album subtype heading
	//elgg_register_plugin_hook_handler('ubertags:subtype:heading', 'album', 'ubertags_subtype_album_handler');
	
	// Icon handlers
	elgg_register_plugin_hook_handler('ubertags:timeline:icon', 'blog', 'ubertags_timeline_blog_icon_handler');
	elgg_register_plugin_hook_handler('ubertags:timeline:icon', 'image', 'ubertags_timeline_image_icon_handler');
	elgg_register_plugin_hook_handler('ubertags:timeline:icon', 'ubertag', 'ubertags_timeline_ubertag_icon_handler');

	// Change how photos are retrieved for the timeline 
	elgg_register_plugin_hook_handler('ubertags:timeline:subtype', 'image', 'ubertags_timeline_photo_override_handler');
	
	// Change display of photos
	elgg_register_plugin_hook_handler('ubertags:subtype', 'image', 'ubertags_photo_override_handler');
	
	// Register blog subtype handler
	elgg_register_plugin_hook_handler('ubertags:subtype', 'blog', 'ubertags_blog_display');

	// Register type
	register_entity_type('object', 'ubertag');		

	return true;
	
}

/* Ubertags page handler */
function ubertags_page_handler($page) {
	global $CONFIG;
	set_context('ubertags');
	gatekeeper();
	
	// Register autocomplete JS
	$auto_url = elgg_get_site_url() . "vendors/jquery/jquery.autocomplete.min.js";
	elgg_register_js($auto_url, 'jquery.autocomplete');
	
	if (isset($page[0]) && !empty($page[0])) {
		switch ($page[0]) {
			/* BEGIN AJAX ENDPOINTS */
			//@TODO maybe a second page handler for the ajax? 
			case 'loadsubtype':
				// Get inputs
				$search = get_input('search');
				$subtype = get_input('subtype');
				$offset = get_input('offset', NULL);
				echo elgg_view('ubertags/subtype_content', array('subtype' => $subtype, 'search' => $search, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivity':
				// Get inputs
				$activity = get_input('activity');
				$container_guid = get_input('container_guid');
				$offset = get_input('offset', NULL);
				echo elgg_view('ubertags/activity_content', array('activity' => $activity, 'container_guid' => $container_guid, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivitytag':
				// Get inputs
				$activity = get_input('activity');
				$search = get_input('search');
				$offset = get_input('offset', NULL);
				echo elgg_view('ubertags/activity_tag_content', array('activity' => $activity, 'search' => $search, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadcustom':
				// Get inputs
				$group = get_input('group');
				$search = get_input('search');
				$offset = get_input('offset', NULL);
				echo elgg_view('ubertags/custom_content', array('group' => $group, 'search' => $search, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'searchubertag':
				$search = get_input('search');
				$group = get_input('group');
				echo elgg_view('ubertags/search', array('subtype' => $subtype, 'search' => $search));
				// This ia an ajax load, so exit
				exit;
			case 'timeline_feed':
				// Grab a type so we can differentiate
				$type = get_input('type', 'overview');
				$min = get_input('min');
				$max = get_input('max');
				echo elgg_view('ubertags/timeline_results_endpoint', array(
					'guid' => $page[1], 
					'type' => $type,
					'min' => $min,
					'max' => $max
				));
				// This ia an ajax load, so exit
				exit;
			break;
			case 'load_timeline':
				$timeline = get_entity($page[1]);
				echo  elgg_view('ubertags/timeline', array('entity' => $timeline));
				exit; // ajax load, exit
			break;
			/* END AJAX ENDPOINTS */
			/* BEGIN CONTENT */
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
			case 'edit':
				$content_info = ubertags_get_page_content_edit($page[1]);
			break;
			case 'view': 
				// Register JS 
				// HAVE TO HAVE TO HAVE TO HAVE TO LOAD THE JS IN THE HEAD!!!
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/ubertags/lib/ubertags-timeline.js', 'ubertags-timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/ubertags/lib/timeline-popup.js', 'timeline-popup');
				$content_info = ubertags_get_page_content_view($page[1]);
			break;
			case 'timeline':
				// Register the js in the head, because that makes things work.
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/ubertags/lib/ubertags-timeline.js', 'ubertags-timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/ubertags/lib/timeline-popup.js', 'timeline-popup');
				$content_info = ubertags_get_page_content_timeline($page[1]);
			break;
			case 'timeline_image_icon':
				echo elgg_view('ubertags/timeline_image_icon', array('guid' => $page[1]));
				exit;
			break;
			case 'group_activity':
				set_context('group');
				$content_info = ubertags_get_page_content_group_activity($page[1]);
			break;
			case 'activity_tag':
				$content_info = ubertags_get_page_content_activity_tag();
			break;
			case 'custom':
				$content_info = ubertags_get_page_content_custom();
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
			/* END CONTENT */
		}
	} else {
		$content_info = ubertags_get_page_content_list();
	}
	
	$sidebar = isset($content_info['sidebar']) ? $content_info['sidebar'] : '';

	$params = array(
		'content' => elgg_view('navigation/breadcrumbs') . $content_info['content'],
		'sidebar' => $sidebar . elgg_view('ubertags/beta'),
	);
	$body = elgg_view_layout($content_info['layout'], $params);
	
	// Register ubertags JS library
	$url = elgg_view_get_simplecache_url('js', 'ubertags');
	elgg_register_js($url, 'ubertags');

	echo elgg_view_page($content_info['title'], $body, $content_info['layout'] == 'administration' ? 'admin' : 'default');
}
	
/**
 * Setup ubertags submenus
 */
function ubertags_submenus() {
	global $CONFIG;

	// all/yours/friends 
	elgg_add_submenu_item(array('text' => elgg_echo('ubertags:menu:yourubertags'), 
								'href' => elgg_get_site_url() . 'pg/ubertags/' . get_loggedin_user()->username), 'ubertags');
								
	elgg_add_submenu_item(array('text' => elgg_echo('ubertags:menu:friendsubertags'), 
								'href' => elgg_get_site_url() . 'pg/ubertags/friends' ), 'ubertags');

	elgg_add_submenu_item(array('text' => elgg_echo('ubertags:menu:allubertags'), 
								'href' => elgg_get_site_url() . 'pg/ubertags/' ), 'ubertags');
	
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

