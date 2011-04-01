<?php
/**
 * Tag Dashboards start.php
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

elgg_register_event_handler('init', 'system', 'tagdashboards_init');

function tagdashboards_init() {
	global $CONFIG;
	
	// Include helpers
	require_once 'lib/tagdashboards_lib.php';
	require_once 'lib/tagdashboards_hooks.php';
	
	// Extend CSS
	elgg_extend_view('css/screen','tagdashboards/css');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'tagdashboards/admin/css');
	
	// Add tagdashboard activity to sidebar
	elgg_extend_view('group-extender/sidebar','tagdashboards/group_sidebar', 0);
	
	// Extend profile view (pre-18)
	elgg_extend_view('profile_navigation/extend', 'tagdashboards/profile_tab');
	
	// Page handler
	register_page_handler('tagdashboards','tagdashboards_page_handler');

	// Add to tools menu
	add_menu(elgg_echo("tagdashboards"), $CONFIG->wwwroot . 'pg/tagdashboards');

	// Add submenus
	elgg_register_event_handler('pagesetup','system','tagdashboards_submenus');
					
	// Set up url handlers
	elgg_register_event_handler('tagdashboard_url','object', 'tagdashboard');

	// Register actions
	elgg_register_action('tagdashboards/save', $CONFIG->pluginspath . 'tagdashboards/actions/save.php');
	elgg_register_action('tagdashboards/edit', $CONFIG->pluginspath . 'tagdashboards/actions/edit.php');
	elgg_register_action('tagdashboards/delete', $CONFIG->pluginspath . 'tagdashboards/actions/delete.php');
	elgg_register_action('tagdashboards/admin_enable_subtypes', $CONFIG->pluginspath . 'tagdashboards/actions/admin_enable_subtypes.php', 'admin');
	
	// Setup url handler for tag dashboards
	register_entity_url_handler('tagdashboards_url_handler','object', 'tagdashboard');
	
	// Comment handler
	elgg_register_plugin_hook_handler('entity:annotate', 'object', 'tagdashboard_annotate_comments');
	
	// Provide the jquery resize plugin
	elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/vendors/jquery.resize.js', 'jquery.resize');
		
	// Icon handlers
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'blog', 'tagdashboards_timeline_blog_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'image', 'tagdashboards_timeline_image_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'tagdashboard', 'tagdashboards_timeline_tagdashboard_icon_handler');

	// Change how photos are retrieved for the timeline 
	elgg_register_plugin_hook_handler('tagdashboards:timeline:subtype', 'image', 'tagdashboards_timeline_photo_override_handler');
	
	// Change display of photos
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'image', 'tagdashboards_photo_override_handler');
	
	// Register blog subtype handler
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'blog', 'tagdashboards_blog_display');

	// Register type
	register_entity_type('object', 'tagdashboard');		

	return true;
	
}

/* Tag Dashboards page handler */
function tagdashboards_page_handler($page) {
	global $CONFIG;
	set_context('tagdashboards');
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
				echo elgg_view('tagdashboards/subtype_content', array('subtype' => $subtype, 'search' => $search, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivity':
				// Get inputs
				$activity = get_input('activity');
				$container_guid = get_input('container_guid');
				$offset = get_input('offset', NULL);
				echo elgg_view('tagdashboards/activity_content', array('activity' => $activity, 'container_guid' => $container_guid, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivitytag':
				// Get inputs
				$activity = get_input('activity');
				$search = get_input('search');
				$offset = get_input('offset', NULL);
				$subtypes = get_input('subtypes', NULL);
				echo elgg_view('tagdashboards/activity_tag_content', array('activity' => $activity, 'search' => $search, 'offset' => $offset, 'subtypes' => $subtypes));
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadcustom':
				// Get inputs
				$group = get_input('group');
				$search = get_input('search');
				$offset = get_input('offset', NULL);
				$subtypes = get_input('subtypes', NULL);
				echo elgg_view('tagdashboards/custom_content', array('group' => $group, 'search' => $search, 'offset' => $offset, 'subtypes' => $subtypes));
				// This is an ajax load, so exit
				exit;
			break;
			case 'searchtagdashboard':
				$search = get_input('search');
				$type = get_input('type');
				$custom = get_input('custom');
				$subtypes = get_input('subtypes', null);
				echo elgg_view('tagdashboards/search', array('search' => $search, 'type' => $type, 'custom' => $custom, 'subtypes' => $subtypes));
				// This ia an ajax load, so exit
				exit;
			case 'timeline_feed':
				// Grab a type so we can differentiate
				$type = get_input('type', 'overview');
				$min = get_input('min');
				$max = get_input('max');
				echo elgg_view('tagdashboards/timeline_results_endpoint', array(
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
				echo  elgg_view('tagdashboards/timeline', array('entity' => $timeline));
				exit; // ajax load, exit
			break;
			/* END AJAX ENDPOINTS */
			/* BEGIN CONTENT */
			case 'friends': 
				$content_info = tagdashboards_get_page_content_friends(get_loggedin_userid());
			break;
			case 'search':
				$content_info = tagdashboards_get_page_content_search($page[1]);
			break;
			case 'settings':
				admin_gatekeeper();
				set_context('admin');
				$content_info = tagdashboards_get_page_content_admin_settings();
			break;
			case 'edit':
				$content_info = tagdashboards_get_page_content_edit($page[1]);
			break;
			case 'view': 
				// Register JS 
				// HAVE TO HAVE TO HAVE TO HAVE TO LOAD THE JS IN THE HEAD!!!
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/tagdashboards-timeline.js', 'tagdashboards-timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/timeline-popup.js', 'timeline-popup');
				$content_info = tagdashboards_get_page_content_view($page[1]);
			break;
			case 'timeline':
				// Register the js in the head, because that makes things work.
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/tagdashboards-timeline.js', 'tagdashboards-timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/timeline-popup.js', 'timeline-popup');
				$content_info = tagdashboards_get_page_content_timeline($page[1]);
			break;
			case 'timeline_image_icon':
				echo elgg_view('tagdashboards/timeline_image_icon', array('guid' => $page[1]));
				exit;
			break;
			case 'group_activity':
				set_context('group');
				$content_info = tagdashboards_get_page_content_group_activity($page[1]);
			break;
			case 'activity_tag':
				$content_info = tagdashboards_get_page_content_activity_tag();
			break;
			case 'custom':
				$content_info = tagdashboards_get_page_content_custom();
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
				$content_info = tagdashboards_get_page_content_list($owner->getGUID());
			break;
			/* END CONTENT */
		}
	} else {
		$content_info = tagdashboards_get_page_content_list();
	}
	
	$sidebar = isset($content_info['sidebar']) ? $content_info['sidebar'] : '';

	$params = array(
		'content' => elgg_view('navigation/breadcrumbs') . $content_info['content'],
		'sidebar' => $sidebar . elgg_view('tagdashboards/beta'),
	);
	$body = elgg_view_layout($content_info['layout'], $params);
	
	// Register tag dashboards JS library
	$url = elgg_view_get_simplecache_url('js', 'tagdashboards');
	elgg_register_js($url, 'tagdashboards');

	echo elgg_view_page($content_info['title'], $body, $content_info['layout'] == 'administration' ? 'admin' : 'default');
}
	
/**
 * Setup tag dashboard submenus
 */
function tagdashboards_submenus() {
	global $CONFIG;

	// all/yours/friends 
	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:yourtagdashboards'), 
								'href' => elgg_get_site_url() . 'pg/tagdashboards/' . get_loggedin_user()->username), 'tagdashboards');
								
	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:friendstagdashboards'), 
								'href' => elgg_get_site_url() . 'pg/tagdashboards/friends' ), 'tagdashboards');

	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:alltagdashboards'), 
								'href' => elgg_get_site_url() . 'pg/tagdashboards/' ), 'tagdashboards');
	
	// Admin 
	if (isadminloggedin()) {
		elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:title:adminsettings'), 
									'href' => $CONFIG->url . "pg/tagdashboards/settings"), 'admin', 'z');
	}
}
	
/**
 * Populates the ->getUrl() method for an tagdashboard
 *
 * @param ElggEntity entity
 * @return string request url
 */
function tagdashboards_url_handler($entity) {
	global $CONFIG;
	return $CONFIG->url . "pg/tagdashboards/view/{$entity->guid}/";
}

/**
 * Hook into the framework and provide comments on tag dashboards
 *
 * @param unknown_type $hook
 * @param unknown_type $entity_type
 * @param unknown_type $returnvalue
 * @param unknown_type $params
 * @return unknown
 */
function tagdashboard_annotate_comments($hook, $entity_type, $returnvalue, $params) {
	$entity = $params['entity'];
	$full = $params['full'];
	
	if (
		($entity instanceof ElggEntity) &&	// Is the right type 
		($entity->getSubtype() == 'tagdashboard') &&  // Is the right subtype
		($full) // This is the full view
	)
	{
		// Display comments
		return elgg_view_comments($entity);
	}
	
}
