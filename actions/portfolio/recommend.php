<?php
/**
 * Tag Dashboards portfolio recommend action
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);
$remove = get_input('remove', FALSE);

$entity = get_entity($guid);

// Make sure this is a valid object
if (!elgg_instanceof($entity, 'object')) {
	register_error(elgg_echo('tagdashboards:error:invalidentity'));
	forward(REFERER);
}

if (!$remove) {
	if (!$entity->recommended_portfolio) {
		// Ignore access to allow any user to add the recommended metadata
		$ia = elgg_get_ignore_access();
		elgg_set_ignore_access(TRUE);
		$entity->recommended_portfolio = 1;
		$success = $entity->save();
		elgg_set_ignore_access($ia);
	}
} else if ($remove && $entity->getOwnerGUID() == elgg_get_logged_in_user_guid()) {
	// Remove the recommended metadata
	tagdashboards_remove_recommended_metadata($entity->guid);

	// Count items to update view
	$options = array(
		'owner_guid' => elgg_get_logged_in_user_guid(),
		'metadata_name_value_pairs' => array(array(
			'name' => 'recommended_portfolio', 
			'value' => '1', 
			'operand' => '=',
			'case_sensitive' => FALSE
		)),
		'count' => TRUE,
	);

	$count = elgg_get_entities_from_metadata($options);
	echo json_encode(array('count' => (int)$count));
}

if ($success) {
	system_message(elgg_echo('tagdashboards:success:recommendportfolio', array($entity->getOwnerEntity()->name)));
}
forward(REFERER);