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

	// Register and load library
	elgg_register_library('tagdashboards', elgg_get_plugins_path() . 'tagdashboards/lib/tagdashboards.php');
	elgg_load_library('tagdashboards');
	
	// Register CSS
	$td_css = elgg_get_simplecache_url('css', 'tagdashboards/css');
	elgg_register_simplecache_view('css/tagdashboards/css');	
	elgg_register_css('elgg.tagdashboards', $td_css);

	// Register custom theme CSS
	$ui_url = elgg_get_site_url() . 'mod/tagdashboards/vendors/smoothness/jquery-ui-1.7.3.custom.css';
	elgg_register_css('jquery.ui.smoothness', $ui_url);

	// Register datepicker css
	$daterange_css = elgg_get_site_url(). 'mod/tagdashboards/vendors/ui.daterangepicker.css';
	elgg_register_css('jquery.daterangepicker', $daterange_css);

	// Register tag dashboards JS library
	$td_js = elgg_get_simplecache_url('js', 'tagdashboards');
	elgg_register_simplecache_view('js/tagdashboards');	
	elgg_register_js('elgg.tagdashboards', $td_js);

	// Register portfolio JS library
	$p_js = elgg_get_simplecache_url('js', 'portfolio');
	elgg_register_simplecache_view('js/portfolio');	
	elgg_register_js('elgg.portfolio', $p_js);
	elgg_load_js('elgg.portfolio');

	// Register daterange JS library
	$dr_js = elgg_get_simplecache_url('js', 'tddaterange');
	elgg_register_simplecache_view('js/tddaterange');	
	elgg_register_js('elgg.tddaterange', $dr_js);

	// Register timeline JS library
	$timeline_lib_js = elgg_get_site_url(). 'mod/tagdashboards/vendors/timeline_lib/timeline_js/timeline-api.js?bundle=true';
	elgg_register_js('simile.timeline', $timeline_lib_js);

	// Regsiter local timeline JS library
	$timeline_js = elgg_get_simplecache_url('js', 'timeline');
	elgg_register_simplecache_view('js/timeline');
	elgg_register_js('elgg.tagdashboards.timeline', $timeline_js);

	// Register datepicker JS
	$daterange_js = elgg_get_site_url(). 'mod/tagdashboards/vendors/daterangepicker.jQuery.js';
	elgg_register_js('jquery.daterangepicker', $daterange_js);

	// Provide the jquery resize plugin
	$resize_js = elgg_get_site_url() . 'mod/tagdashboards/vendors/jquery.resize.js';
	elgg_register_js($resize_js, 'jquery.resize');

	// Extend Groups profile page
	elgg_extend_view('groups/tool_latest','tagdashboards/group_dashboards');

	// Global CSS
	elgg_extend_view('css/elgg','css/tagdashboards/global');
	elgg_extend_view('css/admin','css/tagdashboards/global');

	// Page handler
	elgg_register_page_handler('tagdashboards','tagdashboards_page_handler');

	// Add to main menu
	$item = new ElggMenuItem('tagdashboards', elgg_echo('tagdashboards'), 'tagdashboards/all');
	elgg_register_menu_item('site', $item);

	// Add submenus
	elgg_register_event_handler('pagesetup','system','tagdashboards_submenus');

	// Set up url handlers
	elgg_register_event_handler('tagdashboard_url','object', 'tagdashboard');

	// notifications
	register_notification_object('object', 'tagdashboard', elgg_echo('tagdashboards:notification:subject'));
	elgg_register_plugin_hook_handler('notify:entity:message', 'object', 'tagdashboards_notify_message');

	// Register actions
	$action_base = elgg_get_plugins_path() . 'tagdashboards/actions/tagdashboards';
	elgg_register_action('tagdashboards/save', "$action_base/save.php");
	elgg_register_action('tagdashboards/portfolio', "$action_base/portfolio.php");
	elgg_register_action('tagdashboards/delete', "$action_base/delete.php");
	elgg_register_action('tagdashboards/subtypes', "$action_base/subtypes.php", 'admin');
	elgg_register_action('tagdashboards/tag', "$action_base/tag.php");

	// Portfolio actions
	$action_base = elgg_get_plugins_path() . 'tagdashboards/actions/portfolio';
	elgg_register_action('portfolio/add', "$action_base/add.php");
	elgg_register_action('portfolio/recommend', "$action_base/recommend.php");

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
	
	// Change display of pages (combine pages_top and pages)
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'page', 'tagdashboards_page_override_handler');

	// Include top level pages when trying to grab pages 
	elgg_register_plugin_hook_handler('tagdashboards:subtype', 'custom', 'tagdashboards_custom_page_override_handler');

	// Register for input/tddaterange view plugin hook 
	elgg_register_plugin_hook_handler('view', 'input/tddaterange', 'tagdashboards_daterange_input_handler');

	// Modify entity menu for recommended portfolio items
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'portfolio_setup_entity_menu', 999);
	
	// Modify general entity menu items
	elgg_register_plugin_hook_handler('register', 'menu:entity', 'tagdashboards_setup_entity_menu', 999);

	// Register simpleicon menu items
	elgg_register_plugin_hook_handler('register', 'menu:simpleicon-entity', 'portfolio_setup_simpleicon_entity_menu');

	// Upgrade Event Handler
	elgg_register_event_handler('upgrade', 'system', 'tagdashboards_run_upgrades');

	// Register type
	elgg_register_entity_type('object', 'tagdashboard');		

	// Add group option
	add_group_tool_option('tagdashboards',elgg_echo('tagdashboard:enablegroup'), TRUE);

	// Profile block hook	
	elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'tagdashboards_owner_block_menu');
	
	// Register Ajax Views
	elgg_register_ajax_view('tagdashboards/module/recommended');

	elgg_register_ajax_view('tagdashboards/portfolio/content');

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
 * 	loadtagdashboard	Load a tagdashboard
 * 	timelinefeed		Load timeline feed content
 *  tags				Tags livesearch
 * @param array $page
 * @return NULL
 */
function tagdashboards_page_handler($page) {
	elgg_set_context('tagdashboards');
	
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
		$custom_titles = get_input('custom_titles');

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
			'custom_titles' => $custom_titles,
		);
		
		// Ajax loads
		switch ($page_type) {
			case 'loadtagdashboard':
				echo tagdashboards_get_load_content($dashboard_options);
				break;
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
				break;
			case 'tags':
				echo tagdashboards_get_json_tags(get_input('term'));
				break;
			default:
				break;
		}
		// Just exit
		exit;
	} else { // Regular request
		// Load CSS
		elgg_load_css('elgg.tagdashboards');
		elgg_load_css('jquery.ui.smoothness');
				
		// Load JS
		elgg_load_js('elgg.tagdashboards');
		elgg_load_js('jquery.resize');
		
		// 'All' breadcrumb
		elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
		
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
				// For some reason the form, lightbox, and timeline JS cannot co-exist here..
				elgg_unregister_js('jquery.form'); // Bye bye form.. don't need it
				elgg_load_js('simile.timeline');
				elgg_load_js('elgg.tagdashboards.timeline');
				$params = tagdashboards_get_page_content_view($page[1]);
				break;
			case 'group_activity':
				elgg_set_context('group');
				$params = tagdashboards_get_page_content_group_activity($page[1]);
				break;
			case 'all':
			default:
				$params = tagdashboards_get_page_content_list();
				break;
		}
		
		$body = elgg_view_layout($params['layout'] ? $params['layout'] : 'content', $params);
		echo elgg_view_page($params['title'], $body);
	}
	return TRUE;
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
			'module_class' => 'tagdashboard-module',
		);
		
		$params = array_merge($params, $module_params);
		$params['limit'] = 6;

		// Default module
	 	return elgg_view('modules/ajaxmodule', $params);
	}
	return false;
}

/** 
 *	Override how pagess are listed to display both 
 *	pages and top level pages
 */
function tagdashboards_page_override_handler($hook, $type, $value, $params) {
	if ($type == 'page') {
		$params['subtypes'] = array('page', 'page_top');

		// Check if anyone wants to change the heading for their subtype
		$subtype_heading = elgg_trigger_plugin_hook('tagdashboards:subtype:heading', $type, array(), false);
		if (!$subtype_heading) {
			// Use default item:object:subtype as this is usually defined 
			$subtype_heading = elgg_echo('item:object:' . $type);
		}
		
		// Ajaxmodule params
		$module_params = array(
			'title' => $subtype_heading,
			'listing_type' => 'simpleicon',
			'restrict_tag' => TRUE,
			'module_type' => 'featured',
			'module_id' => $type,
			'module_class' => 'tagdashboard-module',
		);

		$params = array_merge($params, $module_params);

		// Default module
		$content = elgg_view('modules/ajaxmodule', $params);
		
		return $content;
	}
	return false;
}

/** 
 *	Override how pages are listed when grouped by custom tags to display both 
 *	pages and top level pages
 */
function tagdashboards_custom_page_override_handler($hook, $type, $value, $params) {
	// Check to see if we're including pages
	if (in_array('page', $value)) {
		// Add 'page_top' subtype
		$value[] = 'page_top';
	}
	return $value;
}


/**
 * Handler to add a tag dashboards tab to the tabbed profile 
 */
function tagdashboards_profile_tab_hander($hook, $type, $value, $params) {
	$value[] = 'portfolio';
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

/**
 * Adds an 'add to portfolio' and 'recommend for portfolio' button
 *
 * @param sting  $hook   view
 * @param string $type   input/tags
 * @param mixed  $return  Value
 * @param mixed  $params Params
 *
 * @return array
 */
function portfolio_setup_entity_menu($hook, $type, $return, $params) {
	$entity = $params['entity'];
	
	// Ignore these subtypes
	$exceptions = array(
		'forum',
		'forum_topic',
		'forum_reply',
	);

	if (elgg_instanceof($entity, 'object') && elgg_is_logged_in() && !in_array($entity->getSubtype(), $exceptions)) {		
		// Check if entity has portfolio tag
		$params = array(
			'guid' => $entity->guid,
			'limit' => 1,
			'metadata_name_value_pairs' => array(
				'name' => 'tags', 
				'value' => 'portfolio', 
				'operand' => '=',
				'case_sensitive' => FALSE
			),
			'count' => TRUE
		);

		$has_portfolio_tag = (int)elgg_get_entities_from_metadata($params);
		
		// Check if entity has recommended metadata tag
		$params = array(
			'guid' => $entity->guid,
			'limit' => 1,
			'metadata_name_value_pairs' => array(
				'name' => 'recommended_portfolio', 
				'value' => '1', 
				'operand' => '=',
				'case_sensitive' => FALSE
			),
			'count' => TRUE
		);

		$has_recommended_metadata = (int)elgg_get_entities_from_metadata($params);
		
		if (elgg_get_logged_in_user_guid() == $entity->owner_guid) {			
			// If we don't have the portfolio tag, show the add button
			if (!$has_portfolio_tag) {
				$options = array(
					'name' => 'add_to_portfolio',
					'text' => elgg_echo('tagdashboards:label:addtoportfolio'),
					'title' => 'add_to_portfolio',
					'href' => "#{$entity->guid}",
					'class' => 'portfolio-add',
					'section' => 'actions',
					'priority' => 102,
					'id' => "portfolio-add-{$entity->guid}",
				);
				$return[] = ElggMenuItem::factory($options);
			}
		} else {
			// If we don't have the recommended metadata or the portfolio tag, show the recommend button
			if (!$has_portfolio_tag && !$has_recommended_metadata) {
				$options = array(
					'name' => 'recommend_for_portfolio',
					'text' => elgg_echo('tagdashboards:label:recommendforportfolio'),
					'title' => 'recommend_for_portfolio',
					'href' => "#{$entity->guid}",
					'class' => 'portfolio-recommend',
					'section' => 'actions',
					'priority' => 102,
					'id' => "portfolio-recommend-{$entity->guid}",
				);
				$return[] = ElggMenuItem::factory($options);
			}
		}
	} 
	return $return;
}

/**
 * Adds general items to the entity menu
 *
 * @param sting  $hook   view
 * @param string $type   input/tags
 * @param mixed  $return  Value
 * @param mixed  $params Params
 *
 * @return array
 */
function tagdashboards_setup_entity_menu($hook, $type, $return, $params) {
	$entity = $params['entity'];
	
	// Ignore these subtypes
	$exceptions = array(
		'forum',
	);

	if (elgg_instanceof($entity, 'object') && elgg_is_logged_in() && !in_array($entity->getSubtype(), $exceptions)) {		
		// Check if entity has yearbook tag
		$params = array(
			'guid' => $entity->guid,
			'limit' => 1,
			'metadata_name_value_pairs' => array(
				'name' => 'tags', 
				'value' => 'yearbook', 
				'operand' => '=',
				'case_sensitive' => FALSE
			),
			'count' => TRUE
		);

		$has_yearbook_tag = (int)elgg_get_entities_from_metadata($params);

		// If we don't have the recommended metadata or the portfolio tag, show the recommend button
		if (!$has_yearbook_tag) {
			$options = array(
				'name' => 'tag_for_yearbook',
				'text' => elgg_echo('tagdashboards:label:tagforyearbook'),
				'title' => 'tag_for_yearbook',
				'href' => "#{$entity->guid}",
				'class' => 'yearbook-tag',
				'section' => 'actions',
				'priority' => 100,
				'id' => "yearbook-tag-{$entity->guid}",
			);
			$return[] = ElggMenuItem::factory($options);
		}

		// If 'tag for emag' enabled, add a menu item
		if (elgg_get_plugin_setting('enable_emag', 'tagdashboards')) {
			$params['metadata_name_value_pairs'] = array(
				'name' => 'tags', 
				'value' => 'emagazine', 
				'operand' => '=',
				'case_sensitive' => FALSE
			);
			
			$has_emag_tag = (int)elgg_get_entities_from_metadata($params);
			
			if (!$has_emag_tag) {
				$options = array(
					'name' => 'tag_for_emag',
					'text' => elgg_echo('tagdashboards:label:tagforemag'),
					'title' => 'tag_for_emag',
					'href' => "#{$entity->guid}",
					'class' => 'emag-tag',
					'section' => 'actions',
					'priority' => 101,
					'id' => "emag-tag-{$entity->guid}",
				);
				$return[] = ElggMenuItem::factory($options);
			}
		}
		
		// If 'tag for tgs weekly' enabled, add a menu item
		if (elgg_get_plugin_setting('enable_tgsweekly', 'tagdashboards')) {
			$params['metadata_name_value_pairs'] = array(
				'name' => 'tags', 
				'value' => 'weekly', 
				'operand' => '=',
				'case_sensitive' => FALSE
			);
			
			$has_weekly_tag = (int)elgg_get_entities_from_metadata($params);
			
			if (!$has_weekly_tag) {
				$options = array(
					'name' => 'tag_for_weekly',
					'text' => elgg_echo('tagdashboards:label:tagforweekly'),
					'title' => 'tag_for_weekly',
					'href' => "#{$entity->guid}",
					'class' => 'weekly-tag',
					'section' => 'actions',
					'priority' => 102,
					'id' => "weekly-tag-{$entity->guid}",
				);
				$return[] = ElggMenuItem::factory($options);
			}
		}
	} 
	return $return;
}

/**
 * Register items for the simpleicon entity menu
 *
 * @param sting  $hook   view
 * @param string $type   input/tags
 * @param mixed  $return  Value
 * @param mixed  $params Params
 *
 * @return array
 */
function portfolio_setup_simpleicon_entity_menu($hook, $type, $return, $params) {
	if (get_input('recommended_portfolio')) {
		$entity = $params['entity'];
		
		// Make sure entity belongs to the viewing user
		if ($entity->getOwnerEntity() == elgg_get_logged_in_user_entity()) {
			// Item to add object to portfolio
			$options = array(
				'name' => 'add_to_portfolio',
				'text' => elgg_echo('tagdashboards:label:addtoportfolio'),
				'title' => 'add_to_portfolio',
				'href' => "#{$entity->guid}",
				'class' => 'portfolio-add-profile',
				'section' => 'info',
			);
			$return[] = ElggMenuItem::factory($options);

			// Item to add object to portfolio
			$options = array(
				'name' => 'ignore_portfolio',
				'text' => elgg_echo('tagdashboards:label:ignoreportfolio'),
				'title' => 'ignore_portfolio',
				'href' => "#{$entity->guid}",
				'class' => 'portfolio-ignore-profile',
				'section' => 'info',
				'priority' => 600,
			);
			$return[] = ElggMenuItem::factory($options);
		}
	}
	return $return;
}

/**
 * Process upgrades for the tagdashboards plugin
 */
function tagdashboards_run_upgrades() {
	$path = elgg_get_plugins_path() . 'tagdashboards/upgrades/';
	$files = elgg_get_upgrade_files($path);
	foreach ($files as $file) {
		include "$path{$file}";
	}
}

/**
 * Set the notification message for tag dashboards
 * 
 * @param string $hook    Hook name
 * @param string $type    Hook type
 * @param string $message The current message body
 * @param array  $params  Parameters about the blog posted
 * @return string
 */
function tagdashboards_notify_message($hook, $type, $message, $params) {
	$entity = $params['entity'];
	$to_entity = $params['to_entity'];
	$method = $params['method'];
	if (elgg_instanceof($entity, 'object', 'tagdashboard')) {
		$descr = $entity->description;
		$title = $entity->title;
		$owner = $entity->getOwnerEntity();
		return elgg_echo('tagdashboards:notification:body', array(
			$owner->name,
			$title,
			$descr,
			$entity->getURL()
		));
	}
	return null;
}