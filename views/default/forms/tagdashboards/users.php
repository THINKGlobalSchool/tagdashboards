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

// Load sticky form values
if (elgg_is_sticky_form('tagdashboards-save-form')) {
	$users_value = elgg_get_sticky_value('tagdashboards-save-form', 'users');
}

$users_input = elgg_view('input/userpicker', array(
	'name' => 'users',
	'id' => 'tagdashboards-users-input',
	'value' => $users,
));
	
echo "
<div>	
	$users_input <br /><br />
</div>
";

