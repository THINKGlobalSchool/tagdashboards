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

$content = <<<HTML
	<br />
	<div>
		<label>$jobs_label</label><br />
		$jobs_input
	</div>
HTML;

echo $content;