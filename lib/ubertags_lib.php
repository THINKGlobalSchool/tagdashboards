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
	$content = elgg_view('forms/ubertags/search');
	$content_info['content'] = elgg_view_title($content_info['title']) . $content;
	return $content_info;
}

/* Build content for editing an ubertag */
function ubertags_get_page_content_edit($guid) {
	$ubertag = get_entity($guid);
	if (elgg_instanceof($ubertag, 'object', 'ubertag') && $ubertag->canEdit()) {
		elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
		elgg_push_breadcrumb($ubertag->title, $ubertag->getURL());
		elgg_push_breadcrumb('edit');
		$content_info['title'] = elgg_echo('ubertags:title:edit');
		$content_info['layout'] = 'one_column_with_sidebar';
		$content = elgg_view('forms/ubertags/save', array('entity' => $ubertag));
		$content_info['content'] = elgg_view_title($content_info['title']) . $content;
		return $content_info;
	} else {
		register_error(elgg_echo('ubertags:error:notfound'));
		forward(REFERER);
	}
}

/* Build content for ubertags admin settings */
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
		// Breadcrumbs
		$user = get_entity($user_guid);
		if ($user instanceof ElggGroup) {
			// Got a group
			elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
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
	
	if ($user_guid && ($user_guid != $loggedin_userid)) {
		// do not show content header when viewing other users' posts
		$header = elgg_view('page_elements/content_header_member', array('type' => 'Ubertags'));
	}
		
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
	$owner = get_entity($ubertag->container_guid);
	set_page_owner($owner->getGUID());
	elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
	elgg_push_breadcrumb($owner->name, elgg_get_site_url() . 'pg/ubertags/' . $owner->username);
	elgg_push_breadcrumb($ubertag->title, $ubertag->getURL());
	$content_info['title'] = $ubertag->title;
	$content_info['content'] = elgg_view_entity($ubertag, true);
	$content_info['layout'] = 'one_column_with_sidebar';
	
	return $content_info;
}

/* View an Ubertag in timeline mode */
function ubertags_get_page_content_timeline($guid) {
	$ubertag = get_entity($guid);
	$owner = get_entity($ubertag->container_guid);
	set_page_owner($owner->getGUID());
	elgg_push_breadcrumb(elgg_echo('ubertags:menu:allubertags'), elgg_get_site_url() . 'pg/ubertags');
	elgg_push_breadcrumb($owner->name, elgg_get_site_url() . 'pg/ubertags/' . $owner->username);
	elgg_push_breadcrumb($ubertag->title, $ubertag->getURL());
	$content_info['title'] = $ubertag->title;
	$content_info['content'] = elgg_view('ubertags/timeline', array('entity' => $ubertag));
	$content_info['layout'] = 'one_column_with_sidebar';
	
	return $content_info;
}

/* View a groups content grouped by student activity */
function ubertags_get_page_content_group_activity($guid) {
	$group = get_entity($guid);
	$content_info['title'] = elgg_echo('ubertags:title:groupbyactivity');
	$content_info['layout'] = "one_column_with_sidebar";
	if (elgg_instanceof($group, 'group')) {
		elgg_set_page_owner_guid($guid);
		$content_info['content'] = elgg_view_title($content_info['title']);
		$content_info['content'] .= elgg_view('ubertags/group_activity', array('search' => $vars['search'], 'container_guid' => $guid));
	} else {
		$content_info['content'] = '';
	}
	$content_info['sidebar'] = elgg_view('ubertags/group_sidebar');
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

/**
 * Helper function to convert an elgg entity to a timeline compatible
 * event array
 * @param $entity ElggEntity
 * @param $type string either 'overview' or 'detailed'
 */
function ubertags_entity_to_timeline_event_array($entity, $type) {
	// Allow customization of event data for different entity subtypes
	if (!$event = trigger_plugin_hook('ubertags:event:subtype', $entity->getSubtype(), array('entity' => $entity, 'type' => $type), FALSE)) {
		// Load these no matter what the type, (overview and detailed)
		$event['start'] = date('r', strtotime(strftime("%a %b %d %Y", $entity->time_created))); // full date format
		$event['isDuration'] = FALSE;
	
		if ($type == 'detailed') { // Detailed, will load description, etc..
			$event['title'] = $entity->title; 
			$event['description'] = elgg_view("timeline/{$entity->getSubtype()}", array('entity' => $entity));

			
			// See if any subtypes have registered for an icon
			if (!$icon = trigger_plugin_hook('ubertags:timeline:icon', $entity->getSubtype(), array('entity' => $entity), FALSE)) {
				$icon = elgg_get_site_url() . "mod/ubertags/images/generic_icon.gif";
			}

			$event['icon'] = $icon;
			$event['link'] = $entity->getURL();	

		} 
	}
	return $event;

}

/**
 * Screwy function name I know.. this is a hacked up entity getter
 * function that gets entities with given tag ($params['ubertags_search_term']) and
 * entities with a container guid with given tag. This is mostly for images, but 
 * could work on just about anything. I couldn't do this with any existing elgg
 * core functions, so I have this here custom query.
 *
 * @uses $params['ubertags_search_term']
 * @uses $params['callback'] - pass in a callback, or use none (return just data rows)
 * @return array
 */
function ubertags_get_entities_from_tag_and_container_tag($params) {
	global $CONFIG;
	
	$px = $CONFIG->dbprefix;
	
	$type_subtype_sql = elgg_get_entity_type_subtype_where_sql('e', $params['types'], $params['subtypes'], $params['type_subtype_pairs']);
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
				WHERE (msn1.string = 'tags' AND msv1.string = '{$params['ubertags_search_term']}')
					AND {$type_subtype_sql}
					AND (e.site_guid IN ({$CONFIG->site_guid}))
					AND $access_sql
					$wheres) 
				UNION DISTINCT
				(SELECT e.* FROM {$CONFIG->dbprefix}entities e 
				JOIN {$px}metadata c_table on e.container_guid = c_table.entity_guid 
				JOIN {$px}metastrings cmsn on c_table.name_id = cmsn.id 
				JOIN {$px}metastrings cmsv on c_table.value_id = cmsv.id 
				WHERE (cmsn.string = 'tags' AND cmsv.string = '{$params['ubertags_search_term']}')
					AND {$type_subtype_sql}
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
function ubertags_get_limited_entities_from_rows($rows, $limit = 10) {
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
 * for given ubertag
 * @param int $guid
 * @return mixed 
 */
function ubertags_get_last_content($guid) {
	$ubertag = get_entity($guid);
	
	$subtypes = unserialize($ubertag->subtypes);

	// If we weren't supplied an array of subtypes, use defaults
	if (!is_array($subtypes)) {
		$subtypes = ubertags_get_enabled_subtypes();
	}
	
	$params = array(
		'types' => array('object'),
		'subtypes' => $subtypes,
		'limit' => 1,
		'metadata_name_value_pairs' => array(	'name' => 'tags', 
												'value' => $ubertag->search, 
												'operand' => '=',
												'case_sensitive' => FALSE)
	);
	
	$entities = elgg_get_entities_from_metadata($params);
	
	return $entities[0];
}

/* Helper function to grab an array of predefined group activities */
function ubertags_get_group_activities() {
	return array(
		array(	
			'name' => elgg_echo('ubertags:activity:research'), 
			'tag' => 'research'
		), 
		array(	
			'name' => elgg_echo('ubertags:activity:curriculum'), 
			'tag' => 'curriculum'
		),
		array(	
			'name' => elgg_echo('ubertags:activity:collabco'), 
			'tag' => 'collabco'
		),
		array(	
			'name' => elgg_echo('ubertags:activity:tutorial'), 
			'tag' => 'tutorial'
		),
		array(	
			'name' => elgg_echo('ubertags:activity:society'), 
			'tag' => 'society'
		),
		array(	
			'name' => elgg_echo('ubertags:activity:scribe'), 
			'tag' => 'scribe'
		),
	);
}


/** 
 * Helper function to use with array_filter()
 * to determine if tidypics images are unique
 */
function ubertags_image_unique($object) {
	static $idList = array();
	
	if (in_array($object->getGUID(), $idList)) {
		return false;
	}
	
	$idList[] = $object->getGUID();
	return true;
}
