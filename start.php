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

/************* 1.8 Update To Do's *****************
 * Must:
 * - Profile portfolio
 * - Sort out all the included vendors (jquery, theme etc)
 * - Clean up JS/CSS regs
 * - Clean up language file
 *
 * If possible:
 * - Modules?
 * - Uberviews (maybe replace with module?)
 */


elgg_register_event_handler('init', 'system', 'tagdashboards_init');

function tagdashboards_init() {	
	
	// Register and load library
	elgg_register_library('tagdashboards', elgg_get_plugins_path() . 'tagdashboards/lib/tagdashboards.php');
	elgg_load_library('tagdashboards');
		
	// Register CSS
	$td_css = elgg_get_simplecache_url('css', 'tagdashboards/css');
	elgg_register_css('elgg.tagdashboards', $td_css);
	
	// Register custom theme CSS
	$ui_url = elgg_get_site_url() . 'mod/tagdashboards/vendors/smoothness/jquery-ui-1.7.3.custom.css';
	elgg_register_css($ui_url, 'smoothness');
	
	// Register datepicker css
	$daterange_css = elgg_get_site_url(). 'mod/tagdashboards/vendors/ui.daterangepicker.css';
	elgg_register_css($daterange_css, 'daterange');
		
	// Register tag dashboards JS library
	$td_js = elgg_get_simplecache_url('js', 'tagdashboards');
	elgg_register_js($td_js, 'tagdashboards');
	
	// Register datepicker JS
	$daterange_js = elgg_get_site_url(). 'mod/tagdashboards/vendors/daterangepicker.jQuery.js';
	elgg_register_js($daterange_js, 'jquery.daterangepicker');

	// Provide the jquery resize plugin
	elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/vendors/jquery.resize.js', 'jquery.resize');
	
	// Extend admin view to include some extra styles
	elgg_extend_view('layouts/administration', 'tagdashboards/admin/css');
	
	// Extend groups sidebar
	elgg_extend_view('page/elements/sidebar', 'tagdashboards/group_sidebar');
	
	// Extend profile view (pre-18)
	elgg_extend_view('profile_navigation/extend', 'tagdashboards/profile_tab');
	
	// Extend Groups profile page
	elgg_extend_view('groups/tool_latest','tagdashboards/group_dashboards');
	
	// Page handler
	elgg_register_page_handler('tagdashboards','tagdashboards_page_handler');
	
	// Add to main menu
	$item = new ElggMenuItem('tagdashboards', elgg_echo('tagdashboards'), 'tagdashboards/all');
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
	elgg_register_action('tagdashboards/subtypes', "$action_base/subtypes.php", 'admin');
	
	// Setup url handler for tag dashboards
	elgg_register_entity_url_handler('object', 'tagdashboard', 'tagdashboards_url_handler');
		
	// Icon handlers
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'blog', 'tagdashboards_timeline_blog_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'image', 'tagdashboards_timeline_image_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'tagdashboard', 'tagdashboards_timeline_tagdashboard_icon_handler');

	// Change how photos are retrieved for the timeline 
	elgg_register_plugin_hook_handler('tagdashboards:timeline:subtype', 'image', 'tagdashboards_timeline_photo_override_handler');
	
	// Change display of photos
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'image', 'tagdashboards_photo_override_handler');
	
	// Register type
	elgg_register_entity_type('object', 'tagdashboard');		

	// Add group option
	add_group_tool_option('tagdashboards',elgg_echo('tagdashboard:enablegroup'), TRUE);
	
	// Profile block hook	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'tagdashboards_owner_block_menu');

	return true;
}

/**
 * Tag Dashboards page handler 
 *
 * URLs take the form of
 *  All tag dashboards:       tagdashboards/all
 *  User's tag dashboards:    tagdashboards/owner/<username>
 *  Friends tag dashboards:   tagdashboards/friends/<username>
 *  View tag dashboard:       tagdashboards/view/<guid>/<title>
 *  New tag dashboard:        tagdashboards/add/<guid>
 *  Edit tag dashboard:       tagdashboards/edit/<guid>
 *  Group tag dashboard:      tagdashboards/group/<guid>/all
 * 
 * Special Views
 * @todo docs 
 *
 * AJAX/XHR Types
 *	loadsubtype 		Load subtype content
 * 	loadactivity		Load activity content
 * 	loadactivitytag		Load activity/tag content
 * 	loadcustom			Load custom content
 * 	loadtagdashboard	Load a tagdashboard
 * 	timelinefeed		Load timeline feed content
 * 	loadtimeline		Load timeline via ajax
 *
 * @param array $page
 * @return NULL
 */
function tagdashboards_page_handler($page) {
	elgg_set_context('tagdashboards');
	
	gatekeeper();
	
	$page_type = elgg_extract(0, $page, 'all');
	
	// Different process for ajax request vs regular load
	if (elgg_is_xhr()) { // Is Ajax
		
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
		
		// Ajax loads
		switch ($page_type) {
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
			case 'all':
			default:
				break;
		}
	} else { // Regular request
		
		// Load CSS
		elgg_load_css('elgg.tagdashboards');
				
		switch ($page_type) {
			case 'owner': 
				$user = get_user_by_username($page[1]);
				$params = tagdashboards_get_page_content_list($user->guid);
				break;
			case 'friends': 
				$user = get_user_by_username($page[1]);
				$params = tagdashboards_get_page_content_friends($user->guid);
				break;
			case 'group': 
				$params = tagdashboards_get_page_content_list($page[1]);
				break;
			case 'add':
				$params = tagdashboards_get_page_content_add($page_type, $page[1]);
				break;
			case 'edit':
				$params = tagdashboards_get_page_content_edit($page_type, $page[1]);
				break;
			case 'view': 
				// Register JS 
				// HAVE TO HAVE TO HAVE TO HAVE TO LOAD THE JS IN THE HEAD!!!
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/tagdashboards-timeline.js', 'tagdashboards-timeline');
				$params = tagdashboards_get_page_content_view($page[1]);
				break;
			case 'timeline':
				// Register the js in the head, because that makes things work.
				elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
				elgg_register_js(elgg_get_site_url() . 'mod/tagdashboards/lib/tagdashboards-timeline.js', 'tagdashboards-timeline');
				$params = tagdashboards_get_page_content_timeline($page[1]);
				break;
			case 'timeline_image_icon':
				echo elgg_view('tagdashboards/timeline_image_icon', array('guid' => $page[1]));
				exit;
				break;
			case 'group_activity':
				elgg_set_context('group');
				$params = tagdashboards_get_page_content_group_activity($page[1]);
				break;
			case 'activity_tag':
				$params = tagdashboards_get_page_content_activity_tag();
				break;
			case 'custom':
				$params = tagdashboards_get_page_content_custom();
				break;
			case 'all':
			default:
				$params = tagdashboards_get_page_content_list();
				break;
		}
		
		$body = elgg_view_layout($params['layout'] ? $params['layout'] : 'content', $params);
		echo elgg_view_page($params['title'], $body);
	}
	return;
}
	
/**
 * Setup tag dashboard submenus
 */
function tagdashboards_submenus() {
	// Add admin link
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'subtypes', 'tagdashboards');
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
function tagdashboards_owner_block_menu($hook, $type, $value, $params) {
	if (elgg_instanceof($params['entity'], 'user')) {
		$url = "tagdashboards/owner/{$params['entity']->username}";
		$item = new ElggMenuItem('tagdashboards', elgg_echo('tagdashboards'), $url);
		$value[] = $item;
	} else {
		if ($params['entity']->tagdashboards_enable == 'yes') {
			$url = "tagdashboards/group/{$params['entity']->guid}/all";
			$item = new ElggMenuItem('tagdashboards', elgg_echo('tagdashboards:label:grouptags'), $url);
			$value[] = $item;
		}
	}
	return $value;
}
