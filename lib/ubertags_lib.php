<?php
/**
 * Ubertags helper functions
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/* Build content for the ubertag search page */
function ubertags_get_page_content_search() {
	$content_info['title'] = elgg_echo('ubertags:title:search');
	$content_info['layout'] = 'one_column_with_sidebar';
	$content = elgg_view('ubertags/ubertags_search_container');
	$content_info['content'] = elgg_view_title($content_info['title']) . $content;
	return $content_info;
}

function ubertags_get_page_content_admin_settings() {
	$content_info['title'] = elgg_echo('ubertags:title:adminsettings');
	$content_info['layout'] = 'administration';
	$content = elgg_view('ubertags/admin/settings');	
	$content_info['content'] = elgg_view_title($content_info['title']) . $content;
	return $content_info;
}

/* Get ubertags listing content */
function ubertags_get_page_content_list($user_guid = null) {
	if ($user_guid) {
		$user = get_entity($user_guid);
		if ($user instanceof ElggGroup) {
			// Got a group
			elgg_push_breadcrumb(elgg_echo('groups'), elgg_get_site_url() . 'pg/groups');
			elgg_push_breadcrumb($user->name, elgg_get_site_url() . 'pg/ubertags/' . $user->username);
			elgg_push_breadcrumb(elgg_echo('ubertags:label:grouptags'));
			$container_guid = "?container_guid=" .$user->getGUID();
		} else {
			elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
			elgg_push_breadcrumb($user->name, elgg_get_site_url() . 'pg/ubertags/' . $user->username);
		}
		$header_context = 'mine';
		$content = elgg_list_entities(array('type' => 'object', 'subtype' => 'ubertag', 'full_view' => false, 'container_guid' => $user_guid));
		$content_info['title'] = elgg_echo('ubertags:menu:yourubertags');
	} else {
	 	$header_context = 'everyone';
		$content = elgg_list_entities(array('type' => 'object', 'subtype' => 'ubertag', 'full_view' => false));
		$content_info['title'] = elgg_echo('ubertags:menu:allubertags');
	}
	
	// If theres no content, display a nice message
	if (!$content) {
		$content = elgg_view('ubertags/noresults');
	}
		
	$header = elgg_view('page_elements/content_header', array(
		'context' => $header_context,
		'type' => 'ubertag',
		'all_link' => elgg_get_site_url() . "pg/ubertags",
		'mine_link' => elgg_get_site_url() . "pg/ubertags/" . get_loggedin_user()->username,
		'friend_link' => elgg_get_site_url() . "pg/ubertags/friends",
		'new_link' => elgg_get_site_url() . "pg/ubertags/search" . $container_guid,
	));
	
	
	$content_info['content'] = $header . $content;
	$content_info['layout'] = 'one_column_with_sidebar';
	return $content_info;
}

/* Get friends docs */
function ubertags_get_page_content_friends($user_guid) {
	global $CONFIG;
	$user = get_entity($user_guid);
	elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
	elgg_push_breadcrumb($user->name, elgg_get_site_url() . 'pg/ubertags/' . $user->username);
	elgg_push_breadcrumb(elgg_echo('friends'));
	
	$content = elgg_view('page_elements/content_header', array(
		'context' => 'friends',
		'type' => 'ubertag',
		'all_link' => elgg_get_site_url() . "pg/ubertags",
		'mine_link' => elgg_get_site_url() . "pg/ubertags/" . get_loggedin_user()->username,
		'friend_link' => elgg_get_site_url() . "pg/ubertags/friends",
		'new_link' => elgg_get_site_url() . "pg/ubertags/search"
	));

	if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
		$content .= elgg_echo('friends:none:you');
	} else {
		$options = array(
			'type' => 'object',
			'subtype' => 'ubertag',
			'full_view' => FALSE,
		);

		foreach ($friends as $friend) {
			$options['container_guids'][] = $friend->getGUID();
		}
		
		$content_info['title'] = elgg_echo('ubertags:menu:friendsubertags');
		
		$list = elgg_list_entities($options);
		if (!$list) {
			$content .= elgg_view('ubertags/noresults');
		} else {
			$content .= $list;
		}
	}
	$content_info['content'] = $content;
	$content_info['layout'] = 'one_column_with_sidebar';
	
	return $content_info;
}

/* View an Ubertag */
function ubertags_get_page_content_view($guid) {
	$ubertag = get_entity($guid);
	$content_info['title'] = $ubertag->title;
	$content_info['content'] = elgg_view_entity($ubertag, true);
	$content_info['layout'] = 'one_column_with_sidebar';
	
	return $content_info;
}

/* Helper function tog grab the plugins enabled subtypes */
function ubertags_get_enabled_subtypes() {
	return unserialize(get_plugin_setting('enabled_subtypes', 'ubertags'));
}

/* Get all registered subtypes (for admins) */
function ubertags_get_site_subtypes() {
	
	
	// Set up some exceptions
	$exceptions = array(
		'plugin', 
		'widget', 
		'sitepages_page', 
		'page_top', 
		'test_subtype',
		'site'
	);
	
	// Allow exceptions to be modified
	$exceptions = trigger_plugin_hook('ubertags','exceptions', array(), $exceptions);
		
	// Query to grab subtypes
	$query = "SELECT subtype FROM elgg_entity_subtypes WHERE type = 'object';";
	
	// Execute
	$subtypes = get_data($query, 'ubertags_get_site_subtype_callback');
	
	// Filter exceptions	
	$filtered_subtypes = array_diff($subtypes, $exceptions);
	
	return $filtered_subtypes;
}

/**
 *  Callback to handle results from the ubertags_get_subtypes() query 
 *  turns the result from a stdClass to a string
 */
function ubertags_get_site_subtype_callback($data) {
	return $data->subtype;
}

?>