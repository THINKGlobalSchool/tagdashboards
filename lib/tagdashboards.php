<?php
/**
 * Tag Dashboards helper functions
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

/**
 * Build content for editing/creating a tag dashboards
 */
function tagdashboards_get_page_content_edit($page, $guid) { 
	elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
	$params['filter'] = false;
	$params['buttons'] = false;
	
	// General form vars
	$form_vars = array(
		'id' => 'tagdashboards-save-form', 
		'name' => 'tagdashboards-save-form'
	);
	
	if ($page == 'edit') {
		$tagdashboard = get_entity($guid);
		
		$params['title'] = elgg_echo('tagdashboards:title:edit');
		
		if (elgg_instanceof($tagdashboard, 'object', 'tagdashboard') && $tagdashboard->canEdit()) {
			$owner = get_entity($tagdashboard->container_guid);
			
			elgg_set_page_owner_guid($owner->getGUID());
			
			elgg_push_breadcrumb($tagdashboard->title, $tagdashboard->getURL());
			elgg_push_breadcrumb('edit');

			$body_vars = tagdashboards_prepare_form_vars($tagdashboard);

			$params['content'] = elgg_view_form('tagdashboards/save', $form_vars, $body_vars);
		} else {
			register_error(elgg_echo('tagdashboards:error:notfound'));
			forward(REFERER);
		}
	} else {
		if (!$guid) {
			$container = elgg_get_logged_in_user_entity();
		} else {
			$container = get_entity($guid);
		}
		
		elgg_push_breadcrumb(elgg_echo('search'));
		
		$params['title'] = elgg_echo('tagdashboards:title:search');
		
		$body_vars = tagdashboards_prepare_form_vars();
		
		$params['content'] = elgg_view_form('tagdashboards/save', $form_vars, $body_vars);
	}	
	return $params;
}

/**
 * Get tag dashboard listing content 
 * @todo breadcrumbs 
 */
function tagdashboards_get_page_content_list($container_guid = null) {
	$logged_in_user_guid = elgg_get_logged_in_user_guid();
	
	$options = array(
		'type' => 'object', 
		'subtype' => 'tagdashboard', 
		'full_view' => false, 
	);

	elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
	
	if ($container_guid) {
		$options['container_guid'] = $container_guid;
		$entity = get_entity($container_guid);
		elgg_push_breadcrumb($entity->name);
		

		
		if (elgg_instanceof($entity, 'group')) {
			$params['filter'] = false;

		} else if ($container_guid == $logged_in_user_guid) {
			$params['filter_context'] = 'mine';
		} else {
			// do not show button or select a tab when viewing someone else's posts
			$params['filter_context'] = 'none';
		}
		
		$content = elgg_list_entities($options);
		$params['title'] = elgg_echo('tagdashboards:title:owneddashboards', array($entity->name));
			
	} else {
		
		$content = elgg_list_entities($options);
		$params['title'] = elgg_echo('tagdashboards:menu:alltagdashboards');
		$params['filter_context'] = 'all';
	}
	
	// If theres no content, display a nice message
	if (!$content) {
		$content = elgg_view('tagdashboards/noresults');
	}
		
	$params['content'] = $content;
	return $params;
}

/**
 * Get friends tagdashboards 
 */
function tagdashboards_get_page_content_friends($user_guid) {
	$user = get_user($user_guid);
	
	$params['filter_context'] = 'friends';
	$params['title'] = elgg_echo('tagdashboards:menu:friendstagdashboards');
	
	elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
	elgg_push_breadcrumb($user->name, elgg_get_site_url() . 'tagdashboards/owner/' . $user->username);
	elgg_push_breadcrumb(elgg_echo('friends'));


	if (!$friends = get_user_friends($user_guid, ELGG_ENTITIES_ANY_VALUE, 0)) {
		$content .= elgg_echo('friends:none:you');
	} else {
		$options = array(
			'type' => 'object',
			'subtype' => 'tagdashboard',
			'full_view' => FALSE,
		);

		foreach ($friends as $friend) {
			$options['container_guids'][] = $friend->getGUID();
		}
		
		$list = elgg_list_entities($options);
		if (!$list) {
			$content .= elgg_view('tagdashboards/noresults');
		} else {
			$content .= $list;
		}
	}
	$params['content'] = $content;
	
	return $params;
}

/**
 * View a tagdashboard 
 */
function tagdashboards_get_page_content_view($guid) {
	$tagdashboard = get_entity($guid);
	$container = get_entity($tagdashboard->container_guid);
	elgg_set_page_owner_guid($container->getGUID());
	elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
	elgg_push_breadcrumb($container->name, elgg_get_site_url() . 'tagdashboards/' . $container->username);
	elgg_push_breadcrumb($tagdashboard->title, $tagdashboard->getURL());
	$params['title'] = $tagdashboard->title;
	$params['content'] = elgg_view_entity($tagdashboard, true);	
	$params['layout'] = 'one_column';
	return $params;
}

/** 
 * View a tagdashboard in timeline mode 
 */
function tagdashboards_get_page_content_timeline($guid) {
	$tagdashboard = get_entity($guid);
	$owner = get_entity($tagdashboard->container_guid);
	set_page_owner($owner->getGUID());
	elgg_push_breadcrumb(elgg_echo('tagdashboards:menu:alltagdashboards'), elgg_get_site_url() . 'tagdashboards');
	elgg_push_breadcrumb($owner->name, elgg_get_site_url() . 'tagdashboards/' . $owner->username);
	elgg_push_breadcrumb($tagdashboard->title, $tagdashboard->getURL());
	$params['title'] = $tagdashboard->title;
	$params['content'] = elgg_view('tagdashboards/timeline', array('entity' => $tagdashboard));
	
	return $params;
}

/**
 * View a groups content grouped by student activity 
 */
function tagdashboards_get_page_content_group_activity($guid) {
	$group = get_entity($guid);
	$params['title'] = elgg_echo('tagdashboards:title:groupbyactivity');
	if (elgg_instanceof($group, 'group')) {
		elgg_set_page_owner_guid($guid);
		$params['content'] = elgg_view_title($params['title']);
		$params['content'] .= elgg_view('tagdashboards/group_activity', array('container_guid' => $guid));
	} else {
		$params['content'] = '';
	}
	$params['sidebar'] = elgg_view('tagdashboards/group_sidebar');
	return $params;
}

/**
 * View content grouped by student activity with specified tag 
 */
function tagdashboards_get_page_content_activity_tag() {
	$search = get_input('search');
	$params['title'] = elgg_echo('tagdashboards:title:activitytag');
	$params['content'] = elgg_view_title($params['title']);
	$params['content'] .= elgg_view('tagdashboards/activity_tag', array('search' => $search));
	return $params;
}

/**
 * View content grouped by user defined tag with specified search 
 */
function tagdashboards_get_page_content_custom() {
	$search = get_input('search');
	$params['title'] = elgg_echo('tagdashboards:title:custom');
	$params['content'] = elgg_view_title($params['title']);
	$params['content'] .= elgg_view('tagdashboards/custom', array('search' => $search));
	return $params;
}

/**
 * Get JSON tags for autocomplete
 * 
 * @param string $term
 * @return string
 */
function tagdashboards_get_json_tags($term) {
	// Only grab tags similar to the input
	$wheres[] = "msv.string like '%$term%'";	

	// Get site tags
	$site_tags = elgg_get_tags(array(
		'threshold' => 0, 
		'limit' => 99999,
		'wheres' => $wheres,
	));

	$tags_array = array();
	foreach ($site_tags as $site_tag) {
		$tags_array[] = $site_tag->tag;
	}

	return json_encode($tags_array);
}

/**
 * Pull together tag dashboard variables for the save form
 *
 * @param ElggObject $tagdashboard
 * @return array
 */
function tagdashboards_prepare_form_vars($tagdashboard = NULL) {
	// input names => defaults
	$values = array(
		'title' => NULL,
		'description' => NULL,
		'tags' => NULL,
		'access_id' => NULL,
		'search' => NULL,
		'custom_tags' => NULL,
		'owner_guids' => NULL,
		'lower_date' => NULL,
		'upper_date' => NULL,
		'container_guid' => NULL,
		'groupby' => 'subtype',
		'guid' => NULL,
	);
	
	
	if ($tagdashboard) {
		foreach (array_keys($values) as $field) {
			if (isset($tagdashboard->$field)) {
				$values[$field] = $tagdashboard->$field;
			}
		}
	}

	if (elgg_is_sticky_form('tagdashboards-save-form')) {
		$sticky_values = elgg_get_sticky_values('tagdashboards-save-form');
		foreach ($sticky_values as $key => $value) {
			$values[$key] = $value;
		}
	}
	
	elgg_clear_sticky_form('tagdashboards-save-form');

	return $values;
}

/**
 * Build content for an ajax loaded tagdashboard with given options
 * @param Array $options: 
 *
 * search => NULL|string search for tag
 * 
 * type => string type of search (custom, activity, subtype (default))
 * 
 * subtypes => NULL|array of subtypes to include
 * 
 * owner_guids => NULL|array of owner guids to filter on
 *
 * custom_tags => NULL|array of custom tags to group content on (used with type: custom)
 * 
 * @return string 
 */
function tagdashboards_get_load_content($options) {
	switch ($options['type']) {
		case 'activity': 
			$content = elgg_view('tagdashboards/activity_tag', $options);
		break;
		case 'custom': 
			$content = elgg_view('tagdashboards/custom', $options);
		break;
		default: 
		case 'subtype': 
			$content = elgg_view('tagdashboards/subtypes', $options);
		break;
	}
	echo $content;
}

/**
 * Helper function tog grab the plugins enabled subtypes 
 */
function tagdashboards_get_enabled_subtypes() {
	return unserialize(get_plugin_setting('enabled_subtypes', 'tagdashboards'));
}

/* Get all registered subtypes (for admins) */
function tagdashboards_get_site_subtypes() {
	
	
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
	$exceptions = trigger_plugin_hook('tagdashboards','exceptions', array(), $exceptions);
		
	// Query to grab subtypes
	$query = "SELECT subtype FROM elgg_entity_subtypes WHERE type = 'object';";
	
	// Execute
	$subtypes = get_data($query, 'tagdashboards_get_site_subtype_callback');
	
	// Filter exceptions	
	$filtered_subtypes = array_diff($subtypes, $exceptions);
	
	return $filtered_subtypes;
}

/**
 *  Callback to handle results from the tagdashboards_get_subtypes() query 
 *  turns the result from a stdClass to a string
 */
function tagdashboards_get_site_subtype_callback($data) {
	return $data->subtype;
}

/**
 * Helper function to convert an elgg entity to a timeline compatible
 * event array
 * @param $entity ElggEntity
 * @param $type string either 'overview' or 'detailed'
 */
function tagdashboards_entity_to_timeline_event_array($entity, $type) {
	// Allow customization of event data for different entity subtypes
	if (!$event = trigger_plugin_hook('tagdashboards:event:subtype', $entity->getSubtype(), array('entity' => $entity, 'type' => $type), FALSE)) {
		// Load these no matter what the type, (overview and detailed)
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $entity->time_created))); // full date format
		$event['isDuration'] = FALSE;
	
		if ($type == 'detailed') { // Detailed, will load description, etc..
			$event['title'] = $entity->title; 
			$event['description'] = elgg_view("timeline/{$entity->getSubtype()}", array('entity' => $entity));

			
			// See if any subtypes have registered for an icon
			if (!$icon = trigger_plugin_hook('tagdashboards:timeline:icon', $entity->getSubtype(), array('entity' => $entity), FALSE)) {
				$icon = elgg_get_site_url() . "mod/tagdashboards/images/generic_icon.gif";
			}

			$event['icon'] = $icon;
			$event['link'] = $entity->getURL();	

		} 
	}
	return $event;

}

/**
 * Screwy function name I know.. this is a hacked up entity getter
 * function that gets entities with given tag ($params['tagdashboards_search_term']) and
 * entities with a container guid with given tag. This is mostly for images, but 
 * could work on just about anything. I couldn't do this with any existing elgg
 * core functions, so I have this here custom query.
 *
 * @uses $params['tagdashboards_search_term']
 * @uses $params['callback'] - pass in a callback, or use none (return just data rows)
 * @return array
 */
function tagdashboards_get_entities_from_tag_and_container_tag($params) {
	global $CONFIG;
	
	$px = $CONFIG->dbprefix;
		
	$type_subtype_sql = elgg_get_entity_type_subtype_where_sql('e', $params['types'], $params['subtypes'], $params['type_subtype_pairs']);
	
	if (is_array($params['owner_guids'])) {
		$owner_guids_sql = " AND " . elgg_get_entity_owner_where_sql('e', $params['owner_guids']) . " ";
	}
	
	if ((int)$params['created_time_upper'] && (int)$params['created_time_lower']) {
		$date_sql = " AND " . elgg_get_entity_time_where_sql(
			'e', 
			$params['created_time_upper'],
			$params['created_time_lower'], 
			$params['modified_time_upper'], 
			$params['modified_time_lower']
		);
	}

	$access_sql = get_access_sql_suffix('e');
	
	// Include additional wheres
	if ($params['wheres']) {
		foreach($params['wheres'] as $where) {
			$wheres .= " AND $where";
		}
	}
		
	$query =   "(SELECT e.* FROM {$CONFIG->dbprefix}entities e 
				JOIN {$px}metadata n_table1 on e.guid = n_table1.entity_guid 
				JOIN {$px}metastrings msn1 on n_table1.name_id = msn1.id 
				JOIN {$px}metastrings msv1 on n_table1.value_id = msv1.id 
				WHERE (msn1.string = 'tags' AND msv1.string = '{$params['tagdashboards_search_term']}')
					AND {$type_subtype_sql}
					$owner_guids_sql
					$date_sql
					AND (e.site_guid IN ({$CONFIG->site_guid}))
					AND $access_sql
					$wheres) 
				UNION DISTINCT
				(SELECT e.* FROM {$CONFIG->dbprefix}entities e 
				JOIN {$px}metadata c_table on e.container_guid = c_table.entity_guid 
				JOIN {$px}metastrings cmsn on c_table.name_id = cmsn.id 
				JOIN {$px}metastrings cmsv on c_table.value_id = cmsv.id 
				WHERE (cmsn.string = 'tags' AND cmsv.string = '{$params['tagdashboards_search_term']}')
					AND {$type_subtype_sql}
					$owner_guids_sql
					$date_sql
					AND (e.site_guid IN ({$CONFIG->site_guid}))
					AND $access_sql
					$wheres) ";
																							
	if (!$params['count']) {
		$query .= " ORDER BY time_created desc";
		
		if ($params['limit']) {
			$limit = sanitise_int($params['limit']);
			$offset = sanitise_int($params['offset']);
			$query .= " LIMIT $offset, $limit";
		}
		$dt = get_data($query, $params['callback']);			
		return $dt;
	} else {
		$dt = get_data($query);
		return count($dt);
	}
}

/** 
 * Helper function that takes rows from a query and creates no more than 
 * a defined limit of those entities created on the same day
 * @param array $rows
 * @param int $limit
 * @return array
 */
function tagdashboards_get_limited_entities_from_rows($rows, $limit = 10) {
	// Make sure limit is a positive number
	if ((int)$limit <= 0) {
		$limit = 10;
	}
	
	// Limited entities array
	$entities = array();
	
	// Counter to keep track of how many rows share the same DAY
	$date_counter = 0;
	
	// Limiting loop
	foreach($rows as $key => $row) {
		$row_date = date("m.d.y", $row->time_created); // Get item create DAY
		if ($key != 0) {
			// If we're not on the first index, check the DAY of the previous row
			if ($row_date == date("m.d.y", $rows[$key - 1]->time_created)){
				// If this rows DAY is the same as the previous, increment counter
				$date_counter++;
			} else {
				// New DAY, reset counter
				$date_counter = 0;
			}
		}
		// If we haven't reached our limit of 10, create entity and add it to the array
		if ($date_counter < $limit) {
			$entities[] = entity_row_to_elggstar($row);
		}	
	}
	return $entities;
}

/** 
 * Helper function to grab the last/newest entity
 * for given tagdashboard
 * @param int $guid
 * @return mixed 
 */
function tagdashboards_get_last_content($guid) {
	$tagdashboard = get_entity($guid);
	
	$subtypes = unserialize($tagdashboard->subtypes);

	// If we weren't supplied an array of subtypes, use defaults
	if (!is_array($subtypes)) {
		$subtypes = tagdashboards_get_enabled_subtypes();
	}
	
	$params = array(
		'types' => array('object'),
		'subtypes' => $subtypes,
		'limit' => 1,
		'metadata_name_value_pairs' => array(	'name' => 'tags', 
												'value' => $tagdashboard->search, 
												'operand' => '=',
												'case_sensitive' => FALSE)
	);
	
	$entities = elgg_get_entities_from_metadata($params);
	
	return $entities[0];
}

/**
 * Helper function to grab an array of predefined group activities 
 */
function tagdashboards_get_activities() {
	return array(
		array(	
			'name' => elgg_echo('tagdashboards:activity:research'), 
			'tag' => 'research'
		), 
		array(	
			'name' => elgg_echo('tagdashboards:activity:curriculum'), 
			'tag' => 'curriculum'
		),
		array(	
			'name' => elgg_echo('tagdashboards:activity:collabco'), 
			'tag' => 'collabco'
		),
		array(	
			'name' => elgg_echo('tagdashboards:activity:tutorial'), 
			'tag' => 'tutorial'
		),
		array(	
			'name' => elgg_echo('tagdashboards:activity:society'), 
			'tag' => 'society'
		),
		array(	
			'name' => elgg_echo('tagdashboards:activity:scribe'), 
			'tag' => 'scribe'
		),
	);
}

/** HOOKS */

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

		$rows = tagdashboards_get_entities_from_tag_and_container_tag($params['params']);
		return tagdashboards_get_limited_entities_from_rows($rows);
	}
	return false;
}

/** 
 *	Override how photo's are listed to display both 
 *	photos and photos in albums with searched tag
 *	Uses: tagdashboards_get_images_and_albums_with_tag()
 */
function tagdashboards_photo_override_handler($hook, $type, $value, $params) {
	if ($type == 'image') {
		$params['params']['tagdashboards_search_term'] = $params['search']; // Need to set this to use the hacky function
		$params['params']['callback'] = "entity_row_to_elggstar";		
		return elgg_list_entities($params['params'], 'tagdashboards_get_entities_from_tag_and_container_tag');
	}
	return false;
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
 * Example for exceptions 
 */
function tagdashboards_exception_example($hook, $type, $value, $params) {
	// Unset a type (includes it in the list)
	unset($value[array_search('plugin', $value)]);
	
	// Add a new exception
	$value[] = 'todo';
	return $value;
}

/**
 * Example for subtypes 
 */
function tagdashboards_subtype_example($hook, $type, $value, $params) {
	// return custom content
	return "Test";
}


/** 
 * Helper function to use with array_filter()
 * to determine if tidypics images are unique
 */
function tagdashboards_image_unique($object) {
	static $idList = array();
	
	if (in_array($object->getGUID(), $idList)) {
		return false;
	}
	
	$idList[] = $object->getGUID();
	return true;
}