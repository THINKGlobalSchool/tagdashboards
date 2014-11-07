<?php
/**
 * Tag Dashboards add tag popup form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$guid = get_input('guid');

$entity = get_entity($guid);

if (elgg_instanceof($entity, 'object')) {
	$content = elgg_view_form('tagdashboards/addtag', array(), array('entity' => $entity));
} else {
	$content = elgg_echo('tagdashboards:error:invalidentity');
}

echo "<div>{$content}</div>";