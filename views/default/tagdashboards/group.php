<?php
/**
 * Tag Dashboards group subtypes content container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Grab subtypes if they were supplied
$subtypes = $vars['subtypes'];

// Get search
$search = $vars['search'];

// Owner guids
$owner_guids = $vars['owner_guids'];

// Container guid
$container_guid = $vars['container_guid'];

// Dates
$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = tagdashboards_get_enabled_subtypes();
}

// Loop over and display each

foreach ($subtypes as $subtype) {		
	$entity_params = array(
		'created_time_upper' => $upper_date,
		'created_time_lower' => $lower_date,
		'owner_guids' => $owner_guids,
		'container_guid' => $container_guid,
		'tag' => rawurldecode($search),
		'types' => array('object'),
		'subtypes' => array($subtype),
		'limit' => 10,
	);
	
	// See if anyone has registered a hook to display their subtype appropriately
	if (!$content = elgg_trigger_plugin_hook('tagdashboards:subtype', $subtype, $entity_params, false)) {	
			
		// Check if anyone wants to change the heading for their subtype
		$subtype_heading = elgg_trigger_plugin_hook('tagdashboards:subtype:heading', $subtype, array(), false);
		if (!$subtype_heading) {
			// Use default item:object:subtype as this is usually defined 
			$subtype_heading = elgg_echo('item:object:' . $subtype);
		}
		
		// Ajaxmodule params
		$module_params = array(
			'title' => $subtype_heading,
			'listing_type' => 'simpleicon',
			'restrict_tag' => FALSE,
			'module_type' => 'featured',
			'module_id' => $subtype,
			'module_class' => 'tagdashboard-module',
		);
		
		$params = array_merge($entity_params, $module_params);
		
		// Default module
		$content = elgg_view('modules/ajaxmodule', $params);
	}
	echo $content;
}
echo "<script>elgg.modules.ajaxmodule.init();</script><div style='clear: both;'></div>";
