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

$page_owner = elgg_get_page_owner_entity();

// Load sticky form values
if (elgg_is_sticky_form('tagdashboards-save-form')) {
	$users_value = elgg_get_sticky_value('tagdashboards-save-form', 'users');
}

$users_input = elgg_view('input/userpicker', array(
	'name' => 'users',
	'id' => 'tagdashboards-users-input',
	'value' => $users,
));

if (elgg_instanceof($page_owner, 'group')) {
	$group_select_label = elgg_echo('tagdashboards:label:selectusersthisgroup');
	$group_toggle_class = 'tagdashboards-toggle-this-group-select';
	$group_toggle_id = $page_owner->guid;
} else {
	$group_select_label = elgg_echo('tagdashboards:label:selectusersfromgroup');
	$group_toggle_class = 'tagdashboards-toggle-group-select';
}

$group_select_input = elgg_view('input/checkbox', array(
	'class' => $group_toggle_class,
	'id' => $group_toggle_id
));

$group_select_url = elgg_normalize_url('ajax/view/tagdashboards/group_select');

$group_select = <<<HTML
	<label>$group_select_label</label>
	$group_select_input
	<a href='$group_select_url' class='tagdashboards-group-select-link hidden'>#</a>
HTML;


$content = <<<HTML
<div><br />	
	$users_input <br />
	$group_select
</div>
HTML;

echo $content;