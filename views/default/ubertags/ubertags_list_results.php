<?php
/**
 * Ubertags search container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$subtypes = ubertags_get_enabled_subtypes();

foreach ($subtypes as $subtype) {
	$results .= elgg_view('ubertags/ubertags_generic_container', array('subtype' => $subtype, 'search' => $vars['search']));
}

echo $results . "<div style='clear: both;'></div>";
