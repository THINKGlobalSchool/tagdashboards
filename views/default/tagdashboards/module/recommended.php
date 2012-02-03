<?php
/**
 * Tag Dashboards Recommended Module (For generic module)
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['user_guid']
 */

$options = array(
	'owner_guid' => $vars['user_guid'],
	'metadata_name_value_pairs' => array(array(
		'name' => 'recommended_portfolio', 
		'value' => '1', 
		'operand' => '=',
		'case_sensitive' => FALSE
	)),
	'full_view' => FALSE,
	'limit' => 5,
);

set_input('ajaxmodule_listing_type', 'simpleicon');
set_input('recommended_portfolio', TRUE);
$items = elgg_list_entities_from_metadata($options);

if (!$items) {
	$items = "<div style='width: 100%; text-align: center; margin: 10px;'><strong>No results</strong></div>";
}

echo $items;