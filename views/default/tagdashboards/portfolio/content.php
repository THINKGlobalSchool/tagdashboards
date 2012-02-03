<?php
/**
 * Tag Dashboards profile tab
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = get_entity(elgg_extract('user_guid', $vars));

if (elgg_instanceof($user, 'user')) {

	// Get subtypes
	$subtypes = tagdashboards_get_entity_subtypes_from_metadata(array(
		'owner_guids' => $user->guid,
		'metadata_name_value_pairs' => array(
			'name' => 'tags', 
			'value' => 'portfolio', 
			'operand' => '=',
			'case_sensitive' => FALSE,
		)
	));

	$tag_portfolio = "Portfolio";

	// Tag Dashboard Content inputs
	$td_type_input = elgg_view('input/hidden', array(
		'name' => 'type', 
		'id' => 'type', 
		'value' => 'subtypes',
	));

	$td_subtypes_input = elgg_view('input/hidden', array(
		'name' => 'subtypes', 
		'id' => 'subtypes', 
		'value' => json_encode($subtypes)
	));

	$td_search_input = elgg_view('input/hidden', array(
		'name' => 'search', 
		'id' => 'search', 
		'value' => $tag_portfolio,
	));

	$td_owner_guids_input = elgg_view('input/hidden', array(
		'name' => 'owner_guids', 
		'id' => 'owner_guids', 
		'value' => json_encode(array($user->guid))
	));

	$content .= <<<HTML
		<div class='clearfix'></div>
		<div class='tagdashboard-container portfolio-left'>
			<div class='tagdashboard-options'>
				$td_type_input
				$td_subtypes_input
				$td_search_input
				$td_owner_guids_input
			</div>
			<div class='tagdashboards-content-container'></div>
		</div>
		<script type='text/javascript'>
			// Init Dashboard
			elgg.tagdashboards.init();
		</script>
HTML;
} else {
	elgg_echo('tagdashboards:error:invaliduser');
}

echo $content;