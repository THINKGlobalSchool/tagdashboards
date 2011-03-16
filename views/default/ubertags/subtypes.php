<?php
/**
 * Ubertags subtypes content container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Grab subtypes if they were supplied
$subtypes = $vars['subtypes'];

// If we weren't supplied an array of subtypes, use defaults
if (!is_array($subtypes)) {
	$subtypes = ubertags_get_enabled_subtypes();
}

// Loop over and display each
foreach ($subtypes as $subtype) {
	$results .= elgg_view('ubertags/subtype_container', array(
		'subtype' => $subtype, 
		'search' => $vars['search']
	));
}

echo $results . "<div style='clear: both;'></div>";
