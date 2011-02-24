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
 * - Might need to rethink the JS handling.. kind of everywhere ATM
 */

function ubertags_init() {
	global $CONFIG;
	
	// Include helpers
	require_once 'lib/ubertags_lib.php';
	require_once 'lib/ubertags_hooks.php';
			
	// Extend CSS
	elgg_extend_view('css/screen','ubertags/css');
	
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

/**
 * Screwy function name I know.. this is a hacked up entity getter
 * function that gets entities with given tag ($params['ubertags_search_term']) and
 * entities with a container guid with given tag. This is mostly for images, but 
 * could work on just about anything. I couldn't do this with any existing elgg
 * core functions, so I have this here custom query.
 *
 * @uses $params['ubertags_search_term']
 * @return array
 */
function ubertags_get_entities_from_tag_and_container_tag($params) {
	global $CONFIG;
	
	$px = $CONFIG->dbprefix;
	
	$type_subtype_sql = elgg_get_entity_type_subtype_where_sql('e', $params['types'], $params['subtypes'], $params['type_subtype_pairs']);
	
		
	if ($params['count']) {	
		$query = "SELECT count(DISTINCT e.guid) as total FROM {$CONFIG->dbprefix}entities e ";
	} else {
		$query = "SELECT DISTINCT e.* FROM {$px}entities e ";
	}
	
	
	$query .=  "JOIN {$px}metadata c_table on e.container_guid = c_table.entity_guid 
				JOIN {$px}metastrings cmsn on c_table.name_id = cmsn.id 
				JOIN {$px}metastrings cmsv on c_table.value_id = cmsv.id 
				JOIN {$px}metadata n_table on e.guid = n_table.entity_guid 
				JOIN {$px}metadata n_table1 on e.guid = n_table1.entity_guid 
				JOIN {$px}metastrings msn1 on n_table1.name_id = msn1.id 
				JOIN {$px}metastrings msv1 on n_table1.value_id = msv1.id 
				WHERE (cmsn.string = 'tags' AND cmsv.string = '{$params['ubertags_search_term']}') 
					OR (((msn1.string = 'tags' AND msv1.string = '{$params['ubertags_search_term']}' AND ( (1 = 1) and n_table1.enabled='yes'))))
					AND {$type_subtype_sql}
					AND (e.site_guid IN (1)) ";
					
	$query .= "AND " . get_access_sql_suffix('e');
								
	if (!$params['count']) {
		$query .= " ORDER BY e.time_created desc LIMIT {$params['offset']}, {$params['limit']}";
		$dt = get_data($query, "entity_row_to_elggstar");
		return $dt;
	} else {
		$total = get_data_row($query);
		return (int)$total->total;
	}
}

/* Ubertags page handler */
function ubertags_page_handler($page) {
	global $CONFIG;
	set_context('ubertags');
	gatekeeper();

	if (isset($page[0]) && !empty($page[0])) {
		switch ($page[0]) {
			case 'test':
			
			global $CONFIG;
			
			$params = array(
				'types' => array('object'),
				'subtypes' => array('image'),
				'full_view' => FALSE,
				'listtypetoggle' => FALSE,
				'listtype' => 'list',
				'pagination' => TRUE,
				'ubertags_search_term' => 'test'
			);
			
			$context = get_context();
			
			set_context('query_dump');
			echo elgg_list_entities($params, 'ubertags_get_entities_from_tag_and_container_tag');
			set_context($context);
			exit;
			break;
			
			
			case 'ajax_load_subtype':
				// Get inputs
				$search = get_input('search');
				$subtype = get_input('subtype');
				$offset = get_input('offset', NULL);
				echo elgg_view('ubertags/ubertags_generic_endpoint', array('subtype' => $subtype, 'search' => $search, 'offset' => $offset));
				// This is an ajax load, so exit
				exit;
			break;
			case 'ajax_load_results':
				$search = get_input('search');
				echo elgg_view('ubertags/ubertags_results_endpoint', array('subtype' => $subtype, 'search' => $search));
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
			case 'load_timeline':
				$timeline = get_entity($page[1]);
				echo  elgg_view('ubertags/timeline', array('entity' => $timeline));
				exit; // ajax load, exit
			break;
			case 'timeline_image_icon':
				echo elgg_view('ubertags/timeline_image_icon', array('guid' => $page[1]));
				exit;
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
		'sidebar' => $sidebar . elgg_view('ubertags/beta'),
	);
	$body = elgg_view_layout($content_info['layout'], $params);

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

register_elgg_event_handler('init', 'system', 'ubertags_init');
?>