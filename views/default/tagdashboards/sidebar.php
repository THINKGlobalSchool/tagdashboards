<?php
/**
 * Tag Dashboards sidebar
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

if (!elgg_get_page_owner_guid()) {
	$featured_dashboards = elgg_get_entities_from_metadata(array(
		'metadata_name' => 'featured_dashboard',
		'metadata_value' => 'yes',
		'type' => 'object',
		'subtype' => 'tagdashboard',
		'limit' => 10,
	));

	if ($featured_dashboards) {

		elgg_push_context('widgets');
		$body = '';
		foreach ($featured_dashboards as $dashboard) {
			$body .= elgg_view_entity($dashboard, array('full_view' => false));
		}
		elgg_pop_context();

		echo elgg_view_module('aside', elgg_echo("tagdashboards:label:featured"), $body);
	}
}