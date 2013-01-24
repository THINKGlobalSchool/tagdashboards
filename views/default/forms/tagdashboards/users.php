<?php
/**
 * Tag Dashboards users form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @uses $vars['value']
 */

$users = $vars['value'];
$users_value = '';

// Create users string from supplied value
if ($users) {
	if (!is_array($users)) {
		$users = array($users);
	}

	foreach($users as $tag) {
		if (!empty($users_value)) {
			$users_value .= ", ";
		}
		if (is_string($tag)) {
			$users_value .= $tag;
		}
	}
}

// Load sticky form values
if (elgg_is_sticky_form('tagdashboards-save-form')) {
	$users_value = elgg_get_sticky_value('tagdashboards-save-form', 'users');
}

$users_input = elgg_view('input/userpicker', array(
	'name' => 'users',
	'id' => 'tagdashboards-users-input',
	'value' => $users_value,
));
	
echo "
<div>	
	$users_input <br /><br />
</div>
";

