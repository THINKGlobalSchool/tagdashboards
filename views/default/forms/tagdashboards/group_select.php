<?php
/**
 * Tag Dashboards group select form view
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @uses $vars['value']
 */

$group_input = elgg_view('input/hidden', array('name' => 'group_guid'));

$group_list = elgg_view_form('tagdashboards/group_list');

$submit = elgg_view('input/submit', array(
	'value' => elgg_echo('tagdashboards:label:select'),
	'id' => 'tagdashboards-group-select-submit',
));

$content = <<<HTML
	<div id='tagdashboards-group-select-container'>
		<div id='tagdashboards-group-select-list'>
			$group_list
		</div>
		<div class='elgg-foot'>
			$group_input
			$submit
		</div>
	</div>
HTML;

echo $content;