<?php
/**
 * Ubertags admin CSS
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Grab subtypes
$subtypes = ubertags_get_site_subtypes();
$enabled = ubertags_get_enabled_subtypes();

$subtypes_table_caption = elgg_echo('ubertags:label:subtypesheading');
$subtype_heading = elgg_echo('ubertags:label:subtype_heading');
$enabled_heading = elgg_echo('ubertags:label:enabled_heading');

$subtypes_table = "<table>
					<caption>$subtypes_table_caption</caption>
					<tr>
						<th>$subtype_heading</th>
						<th>$enabled_heading</th>
					</tr>";
					
foreach($subtypes as $subtype) {
	$checked = '';
	if (in_array($subtype, $enabled)) {
		$checked = "checked";
	}
	$subtypes_table .= "<tr>";
	$subtypes_table .= "<td class='label'>$subtype</td>";
	$subtypes_table .= "<td><input type='checkbox' name='subtypes_enabled[]' value='$subtype' $checked /></td>";
	$subtypes_table .= "</tr>";
}

$subtypes_table .= "</table>";

$subtypes_settings_submit = elgg_view('input/submit', array(
	'internalname' => 'subtypes_settings_submit',
	'internalid' => 'subtypes_settings_submit',
	'value' => elgg_echo('ubertags:label:subtypes_settings_submit')
));

$subtypes_action_url = elgg_add_action_tokens_to_url(elgg_get_site_url() . 'action/ubertags/admin_enable_subtypes');

echo <<<EOT
	<div class="ubertags_settings">
		<form action="$subtypes_action_url" method="POST" name="subtypes_enable">
		$subtypes_table
		$subtypes_settings_submit
		</form>
	</div>
EOT;
?>