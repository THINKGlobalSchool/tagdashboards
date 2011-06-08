<?php
/**
 * Tag Dashboards Subtypes Forms
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Grab subtypes
$subtypes = tagdashboards_get_site_subtypes();
$enabled = tagdashboards_get_enabled_subtypes();

$subtypes_table_caption = elgg_echo('tagdashboards:label:subtypesheading');
$subtype_heading = elgg_echo('tagdashboards:label:subtype_heading');
$enabled_heading = elgg_echo('tagdashboards:label:enabled_heading');

$subtypes_table = "<h3>$subtypes_table_caption</h3><br />
					<table style='width: 35%;' class='elgg-table'>
					<thead>
						<tr>
							<th width='95%'><strong>$subtype_heading</strong></th>
							<th width='5%'><strong>$enabled_heading</strong></th>
						</tr>
					</thead>
					<tbody>";
					
foreach($subtypes as $subtype) {
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked";
	}
	$subtypes_table .= "<tr>";
	$subtypes_table .= "<td>$subtype</td>";
	$subtypes_table .= "<td><input type='checkbox' name='subtypes[]' value='$subtype' $checked /></td>";
	$subtypes_table .= "</tr>";
}

$subtypes_table .= "</tbody></table>";

$subtypes_settings_submit = elgg_view('input/submit', array(
	'name' => 'subtypes_settings_submit',
	'id' => 'subtypes_settings_submit',
	'value' => elgg_echo('tagdashboards:label:subtypes_settings_submit')
));

echo $subtypes_table . $subtypes_settings_submit;
