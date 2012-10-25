<?php
/**
 * Tag Dashboards plugin settings
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Custom jobs input
$jobs_label = elgg_echo('tagdashboards:label:customjobs');
$jobs_input = elgg_view('input/plaintext', array(
	'name' => 'params[customjobs]', 
	'value' => $vars['entity']->customjobs)
);

// Tag for Emag label
$emag_tag_label = elgg_echo('tagdashboards:label:enabletagforemag');
$emag_tag_input = elgg_view('input/dropdown', array(
	'id' => 'enable_emag',
	'name' => 'params[enable_emag]',
	'options_values' => array(
		0 => elgg_echo('No'), 
		1 => elgg_echo('Yes')
	),
	'value' => $vars['entity']->enable_emag,
));

// Tag for TGS Weekly label
$weekly_tag_label = elgg_echo('tagdashboards:label:enabletgsweekly');
$weekly_tag_input = elgg_view('input/dropdown', array(
	'id' => 'enable_tgsweekly',
	'name' => 'params[enable_tgsweekly]',
	'options_values' => array(
		0 => elgg_echo('No'), 
		1 => elgg_echo('Yes')
	),
	'value' => $vars['entity']->enable_tgsweekly,
));

$content = <<<HTML
	<br />
	<div>
		<label>$jobs_label</label><br />
		$jobs_input
	</div>
	<div>
		<label>$emag_tag_label</label>
		$emag_tag_input
	</div>
	<div>
		<label>$weekly_tag_label</label>
		$weekly_tag_input
	</div>
HTML;

echo $content;