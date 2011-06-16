<?php
/**
 * Tag Dashboards group by user defined, based on search tag container
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Custom tags
$custom = $vars['custom_tags'];
$search = $vars['search'];

// Get Subtypes
$subtypes = $vars['subtypes'];

// Get ownerguids
$owner_guids = $vars['owner_guids'];

// Dates
$lower_date = $vars['lower_date'];
$upper_date = $vars['upper_date'];

foreach($custom as $tag) {	
	$params = array(
		'created_time_upper' => $upper_date,
		'created_time_lower' => $lower_date,
		'owner_guids' => $owner_guids,
		'types' => array('object'),
		'subtypes' => $subtypes,
		'limit' => 10,
		'title' => ucfirst($tag),
		'listing_type' => 'simpleicon',
		'restrict_tag' => TRUE,
		'module_type' => 'featured',
		'module_id' => $tag,
		'module_class' => 'tagdashboard-module',
		'tags' => array($search, $tag),
	);
	
	// Default module
	$content = elgg_view('modules/ajaxmodule', $params);
	
	echo $content;
}
echo "<script>elgg.modules.ajaxmodule.init();</script><div style='clear: both;'></div>";
