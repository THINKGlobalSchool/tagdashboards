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
	// Include helpers
	require_once 'lib/tagdashboards.php';
	
	// Extend CSS
	elgg_extend_view('css/screen','tagdashboards/css');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'tagdashboards/admin/css');
	
	// Add tagdashboard activity to sidebar
	elgg_extend_view('group-extender/sidebar','tagdashboards/group_sidebar', 0);
	
	// Extend profile view (pre-18)
	elgg_extend_view('profile_navigation/extend', 'tagdashboards/profile_tab');
	
	// Extend Groups profile page
	elgg_extend_view('groups/tool_latest','tagdashboards/group_dashboards');
	
	// Page handler
	elgg_register_page_handler('tagdashboards','tagdashboards_page_handler');
	
	// Add to main menu
	$item = new ElggMenuItem('tagdashboards', elgg_echo('tagdashboards'), 'tagdashboards');
	elgg_register_menu_item('site', $item);

	// Add submenus
	elgg_register_event_handler('pagesetup','system','tagdashboards_submenus');
					
	// Set up url handlers
	elgg_register_event_handler('tagdashboard_url','object', 'tagdashboard');

	// Register actions
	$action_base = elgg_get_plugins_path() . 'tagdashboards/actions/tagdashboards';
	elgg_register_action('tagdashboards/save', "$action_base/save.php");
	elgg_register_action('tagdashboards/save_tag_portfolio', "$action_base/save_tag_portfolio.php");
	elgg_register_action('tagdashboards/edit', "$action_base/edit.php");
	elgg_register_action('tagdashboards/delete', "$action_base/delete.php");
	elgg_register_action('tagdashboards/admin_enable_subtypes', "$action_base/admin_enable_subtypes.php", 'admin');
	
	// Setup url handler for tag dashboards
	elgg_register_entity_url_handler('object', 'tagdashboard', 'tagdashboards_url_handler');
	
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
	
	// Register type
	register_entity_type('object', 'tagdashboard');		

	// Add group option
	add_group_tool_option('tagdashboards',elgg_echo('tagdashboard:enablegroup'), TRUE);
	
	// Profile hook	
	elgg_register_plugin_hook_handler('profile_menu', 'profile', 'tagdashboards_profile_menu');

	return true;
}

/**
 * Tag Dashboards page handler 
 * 
 *
 * @todo Ajax endpoints - Should be somewhere else? Somehow?
 *	loadsubtype 		Load subtype content
 * 	loadactivity		Load activity content
 * 	loadactivitytag		Load activity/tag content
 * 	loadcustom			Load custom content
 * 	loadtagdashboard	Load a tagdashboard
 * 	timelinefeed		Load timeline feed content
 * 	loadtimeline		Load timeline via ajax
 *
 * Title is ignored
 * @param array $page
 * @return NULL
 */
function tagdashboards_page_handler($page) {
	set_context('tagdashboards');
	gatekeeper();
	
	// Register autocomplete JS
	$auto_url = elgg_get_site_url() . "vendors/jquery/jquery.autocomplete.min.js";
	elgg_register_js($auto_url, 'jquery.autocomplete');
	
	// Register datepicker
	$daterange_url = elgg_get_site_url(). 'mod/tagdashboards/vendors/daterangepicker.jQuery.js';
	elgg_register_js($daterange_url, 'jquery.daterangepicker');
	
	// Register datepicker css
	$daterange_url = elgg_get_site_url(). 'mod/tagdashboards/vendors/ui.daterangepicker.css';
	elgg_register_css($daterange_url, 'daterange');
	
	// Register custom theme CSS
	$ui_url = elgg_get_site_url() . 'mod/tagdashboards/vendors/smoothness/jquery-ui-1.7.3.custom.css';
	elgg_register_css($ui_url, 'smoothness');
	
	// Common options & inputs
	$type = get_input('type', 'subtype');
	$search = get_input('search', NULL);
	$activity = get_input('activity');
	$group = get_input('group');
	$container_guid = get_input('container_guid');
	$subtype = get_input('subtype');
	$subtypes = get_input('subtypes', NULL);
	$offset = get_input('offset', NULL);
	$owner_guids = get_input('owner_guids', NULL);
	$lower_date = get_input('lower_date', NULL);
	$upper_date = get_input('upper_date', NULL);
	$custom_tags = get_input('custom_tags');
	
	$dashboard_options = array(
		'type' => $type,
		'activity' => $activity, 
		'group' => $group, 
		'container_guid' => $container_guid, 
		'subtype' => $subtype,
		'subtypes' => $subtypes, 
		'search' => $search, 
		'offset' => $offset, 
		'owner_guids' => $owner_guids,
		'lower_date' => $lower_date,
		'upper_date' => $upper_date,
		'custom_tags' => $custom_tags,
	);
	
	
	if (isset($page[0]) && !empty($page[0])) {
		switch ($page[0]) {
			/* BEGIN AJAX ENDPOINTS */
			//@TODO maybe a second page handler for the ajax? 
			case 'loadsubtype':				
				echo elgg_view('tagdashboards/content/subtype', $dashboard_options);
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivity':
				echo elgg_view('tagdashboards/content/activity', $dashboard_options);
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadactivitytag':
				echo elgg_view('tagdashboards/content/activity_tag', $dashboard_options);
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadcustom':
				echo elgg_view('tagdashboards/content/custom', $dashboard_options);
				// This is an ajax load, so exit
				exit;
			break;
			case 'loadtagdashboard':
				echo tagdashboards_get_load_content($dashboard_options);
				// This ia an ajax load, so exit
				exit;
			case 'timelinefeed':
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
			case 'loadtimeline':
				$timeline = get_entity($page[1]);
				echo  elgg_view('tagdashboards/timeline', array('entity' => $timeline));
				exit; // ajax load, exit
			break;
			/* END AJAX ENDPOINTS */
			/* BEGIN CONTENT */
			case 'friends': 
				$content_info = tagdashboards_get_page_content_friends(get_loggedin_userid());
			break;
			case 'add':
				if ($page[1]) {
					set_page_owner(get_entity($page[1])->getGUID());
				}
				$content_info = tagdashboards_get_page_content_add($page[1]);
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
	$url = elgg_get_simplecache_url('js', 'tagdashboards');
	elgg_register_js($url, 'tagdashboards');

	echo elgg_view_page($content_info['title'], $body, $content_info['layout'] == 'administration' ? 'admin' : 'default');
}
	
/**
 * Setup tag dashboard submenus
 */
function tagdashboards_submenus() {
	// all/yours/friends 
	/*
	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:yourtagdashboards'), 
								'href' => elgg_get_site_url() . 'tagdashboards/' . get_loggedin_user()->username), 'tagdashboards');
								
	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:friendstagdashboards'), 
								'href' => elgg_get_site_url() . 'tagdashboards/friends' ), 'tagdashboards');

	elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:menu:alltagdashboards'), 
								'href' => elgg_get_site_url() . 'tagdashboards/' ), 'tagdashboards');
	*/

	// Admin 
	if (elgg_is_admin_logged_in()) {
		/*elgg_add_submenu_item(array('text' => elgg_echo('tagdashboards:title:adminsettings'), 
									'href' => elgg_get_site_url() . "tagdashboards/settings"), 'admin', 'z');
		*/
	}
}
	
/**
 * Populates the ->getUrl() method for an tagdashboard
 *
 * @param ElggEntity entity
 * @return string request url
 */
function tagdashboards_url_handler($entity) {
	return elgg_get_site_url() . "tagdashboards/view/{$entity->guid}/";
}

/**
 * Plugin hook to add tagdashboards to the profile block
 * 	
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function tagdashboards_profile_menu($hook, $type, $value, $params) {
	global $CONFIG;

	if ($params['owner'] instanceof ElggUser || $params['owner']->tagdashboards_enable == 'yes') {
		$value[] = array(
			'text' => elgg_echo('tagdashboards'),
			'href' => elgg_get_site_url() . "tagdashboards/{$params['owner']->username}",
		);
	}
	return $value;
}

/**
 * Hook into the framework and provide comments on tag dashboards
 *
 * @param unknown_type $hook
 * @param unknown_type $type
 * @param unknown_type $value
 * @param unknown_type $params
 * @return unknown
 */
function tagdashboard_annotate_comments($hook, $type, $value, $params) {
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
