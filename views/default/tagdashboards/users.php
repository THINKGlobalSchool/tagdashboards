<?php
/**
 * Tag Dashboards group by users, based on search tag container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Users
$users = $vars['user_guids'];

$search = $vars['search'];

// Get Subtypes
$subtypes = $vars['subtypes'];

// Trigger a handler for grabbing subtypes when grouped by custom tags
$subtypes = elgg_trigger_plugin_hook('tagdashboards:subtype', 'custom', NULL, $subtypes);

// Container guid
$container_guid = $vars['container_guid'];

// Dates
$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

$search = urldecode($search);

foreach($users as $idx => $guid) {
	$user = get_entity($guid);

	$title = elgg_view('output/url', array(
		'text' => $user->name,
		'href' => $user->getURL(),
		'target' => '_blank', 
	));
	
	$params = array(
		'created_time_upper' => $upper_date,
		'created_time_lower' => $lower_date,
		'owner_guids' => $guid,
		'container_guid' => $container_guid,
		'types' => array('object'),
		'subtypes' => $subtypes,
		'limit' => 10,
		'title' => $title,
		'listing_type' => 'simpleicon',
		'restrict_tag' => TRUE,
		'albums_images' => TRUE,
		'module_type' => 'featured',
		'module_id' => $guid,
		'module_class' => 'tagdashboard-module',
	);

	// Support for matching on multiple tags
	if (is_array($multi_tags = string_to_tag_array($search))) {
		array_push($multi_tags, $tag);
		$params['tags'] = $multi_tags;
	} else {
		$params['tags'] = array($search, $tag);
	}
	
	// Default module
	$content = elgg_view('modules/ajaxmodule', $params);
	
	echo $content;
}
echo "<script>elgg.modules.ajaxmodule.init();</script><div style='clear: both;'></div>";
