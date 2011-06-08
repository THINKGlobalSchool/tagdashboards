<?php
/**
 * tagdashboard date range input
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['name'] 	Name of the inputs (will have _to or _from appended)
 * @uses $vars['id']	Input ID (will have -to or -from appended)
 * @uses $vars['value_lower'] 	Lower initial value (optional)
 * @uses $vars['value_upper'] 	Upper initial value (optional)
 */

$name 	= $vars['name'];
$id 	= $vars['id'];
$lower 	= $vars['value_lower'] ? date("F j, Y",$vars['value_lower']) : '';
$upper 	= $vars['value_upper'] ? date("F j, Y",$vars['value_upper']) : '';

echo <<<HTML
	<div id='daterange-container' style='position:relative;'>
		<label>From</label>
		<input class='tagdashboards-daterange' type="text" id="{$id}-from" name="lower_date" value="{$lower}" />
		<label>to</label>
		<input class='tagdashboards-daterange' type="text" id="{$id}-to" name="upper_date" value="{$upper}" />
	</div>
HTML;

return;