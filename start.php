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
 * - Clean up language file
 * - Clean up CSS
 * - Check for unused functions
 * - Timeline
 * - Strip out tagdashboard input stuff into its own view?
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
	elgg_register_css('jquery.ui.smoothness', $ui_url);
	
	// Register datepicker css
	$daterange_css = elgg_get_site_url(). 'mod/tagdashboards/vendors/ui.daterangepicker.css';
	elgg_register_css('jquery.daterangepicker', $daterange_css);
		
	// Register tag dashboards JS library
	$td_js = elgg_get_simplecache_url('js', 'tagdashboards');
	elgg_register_js('elgg.tagdashboards', $td_js);
	
	// Register tag dashboards JS library
	$dr_js = elgg_get_simplecache_url('js', 'tddaterange');
	elgg_register_js('elgg.tddaterange', $dr_js);
	
	// Register timeline JS library
	$timeline_lib_js = elgg_get_site_url(). 'mod/tagdashboards/vendors/timeline_2.3.1/webapp/api/timeline-api.js';
	elgg_register_js('simile.timeline', $timeline_lib_js);
	
	// Regsiter local timeline JS library
	$timeline_js = elgg_get_simplecache_url('js', 'timeline');
	elgg_register_js('elgg.tagdashboards.timeline', $timeline_js);
		
	// Register datepicker JS
	$daterange_js = elgg_get_site_url(). 'mod/tagdashboards/vendors/daterangepicker.jQuery.js';
	elgg_register_js('jquery.daterangepicker', $daterange_js);

	// Provide the jquery resize plugin
	$resize_js = elgg_get_site_url() . 'mod/tagdashboards/vendors/jquery.resize.js';
	elgg_register_js($resize_js, 'jquery.resize');
	
	// Extend groups sidebar
	elgg_extend_view('page/elements/sidebar', 'tagdashboards/group_sidebar');
	
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
	elgg_register_action('tagdashboards/tagportfolio', "$action_base/tagportfolio.php");
	elgg_register_action('tagdashboards/delete', "$action_base/delete.php");
	elgg_register_action('tagdashboards/subtypes', "$action_base/subtypes.php", 'admin');
	
	// Setup url handler for tag dashboards
	elgg_register_entity_url_handler('object', 'tagdashboard', 'tagdashboards_url_handler');
		
	// Add a new tab to the tabbed profile
	elgg_register_plugin_hook_handler('tabs', 'profile', 'tagdashboards_profile_tab_hander');	
		
	// Icon handlers
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'blog', 'tagdashboards_timeline_blog_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'image', 'tagdashboards_timeline_image_icon_handler');
	elgg_register_plugin_hook_handler('tagdashboards:timeline:icon', 'tagdashboard', 'tagdashboards_timeline_tagdashboard_icon_handler');

	// Change how photos are retrieved for the timeline 
	elgg_register_plugin_hook_handler('tagdashboards:timeline:subtype', 'image', 'tagdashboards_timeline_photo_override_handler');
	
	// Change display of photos
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'image', 'tagdashboards_photo_override_handler');
	
	// Register for input/tddaterange view plugin hook 
	elgg_register_plugin_hook_handler('view', 'input/tddaterange', 'tagdashboards_daterange_input_handler');
	
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
 *
 * @param array $page
 * @return NULL
 */
function tagdashboards_page_handler($page) {
	elgg_set_context('tagdashboards');

	gatekeeper();
	
	$page_type = elgg_extract(0, $page, 'all');
	
	// Different process for ajax request vs regular load
	if (elgg_is_xhr() || get_input('timeline_override', false)) { // Is Ajax
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
				echo elgg_view('tagdashboards/timeline_feed', array(
					'guid' => $page[1], 
					'type' => $type,
					'min' => $min,
					'max' => $max
				));
				// This ia an ajax load, so exit
				exit;
				break;
			case 'tags':
				echo tagdashboards_get_json_tags(get_input('term'));
				break;
			case 'all':
			default:
				break;
		}
	} else { // Regular request
		
		// Load CSS
		elgg_load_css('elgg.tagdashboards');
		elgg_load_css('jquery.ui.smoothness');

		// Load JS
		elgg_load_js('elgg.tagdashboards');
		elgg_load_js('jquery.resize');
		
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
				$params = tagdashboards_get_page_content_edit($page_type, $page[1]);
				break;
			case 'edit':
				$params = tagdashboards_get_page_content_edit($page_type, $page[1]);
				break;
			case 'view': 
				elgg_load_js('elgg.tagdashboards.timeline');
				elgg_load_js('simile.timeline');
				$params = tagdashboards_get_page_content_view($page[1]);
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
	if (elgg_in_context('admin')) {
		elgg_register_admin_menu_item('administer', 'tagdashboards');
		elgg_register_admin_menu_item('administer', 'subtypes', 'tagdashboards');
	}
}

/** 
 *	Hook to change how photos are retrieved on the timeline
 */
function tagdashboards_timeline_photo_override_handler($hook, $type, $value, $params) {
	if ($type == 'image') {
		$params['params']['tagdashboards_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['limit'] = 0;
		$params['params']['offset'] = 0;
		$params['params']['types'] = array('object');
		$params['params']['subtypes'] = array('image');
		//$params['params']['callback'] = "entity_row_to_elggstar";

		$rows = am_get_entities_from_tag_and_container_tag($params['params']);
		return tagdashboards_get_limited_entities_from_rows($rows);
	}
	return false;
}

/** 
 *	Override how photo's are listed to display both 
 *	photos and photos in albums with searched tag
 *  Uses ajaxmodule with 'albums_images' option
 */
function tagdashboards_photo_override_handler($hook, $type, $value, $params) {
	if ($type == 'image') {
		
		// Ajaxmodule params
		$module_params = array(
			'title' => elgg_echo('item:object:' . $type),
			//'listing_type' => 'simple',
			'albums_images'=> TRUE,
			'module_type' => 'featured',
			'module_id' => $type,
			'module_class' => 'tagdashboards-container',
		);
		
		$params = array_merge($params, $module_params);
		$params['limit'] = 6;

		// Default module
	 	return elgg_view('modules/ajaxmodule', $params);
	}
	return false;
}

/**
 * Handler to add a tag dashboards tab to the tabbed profile 
 */
function tagdashboards_profile_tab_hander($hook, $type, $value, $params) {
	$value[] = 'tagportfolio';
	return $value;
}


/**
 * Handler to register a timeline icon for blogs 
 */
function tagdashboards_timeline_blog_icon_handler($hook, $type, $value, $params) {
	if ($type == 'blog') {
		return elgg_get_site_url() . "mod/tagdashboards/images/blog.gif";
	}
	return false;
}

/**
 * Handler to register a timeline icon for images 
 */
function tagdashboards_timeline_image_icon_handler($hook, $type, $value, $params) {
	if ($type == 'image') {
		return elgg_get_site_url() . "mod/tagdashboards/images/image.gif";
	}
	return false;
}

/** 
 * Handler to register a timeline icon for tag dashboards 
 */
function tagdashboards_timeline_tagdashboard_icon_handler($hook, $type, $value, $params) {
	if ($type == 'tagdashboard') {
		return elgg_get_site_url() . "mod/tagdashboards/images/tagdashboard.gif";
	}
	return false;
}

/**
 * Handler to change name of Albums to Photos 
 */
function tagdashboards_subtype_album_handler($hook, $type, $value, $params) {
	if ($type == 'album') {
		return 'Photos';
	}
}

	
/**
 * Populates the ->getUrl() method for an tagdashboard
 *
 * @param ElggEntity entity
 * @return string request url
 */
function tagdashboards_url_handler($entity) {
	
	$friendly_title = elgg_get_friendly_title($entity->title);
	
	return elgg_get_site_url() . "tagdashboards/view/{$entity->guid}/$friendly_title";
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

/**
 * Plugin hook handler to load daterangepicker JS when 
 * the the input/tddaterange view is loaded
 *
 * @param sting  $hook   view
 * @param string $type   input/tags
 * @param mixed  $value  Value
 * @param mixed  $params Params
 *
 * @return array
 */
function tagdashboards_daterange_input_handler($hook, $type, $value, $params) {
	elgg_load_css('jquery.daterangepicker');
	elgg_load_css('jquery.ui.smoothness');	
	elgg_load_js('jquery.daterangepicker');
	elgg_load_js('elgg.tddaterange');
	return $value;
}

