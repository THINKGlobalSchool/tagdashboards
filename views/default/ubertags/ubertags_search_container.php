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


echo elgg_view('ubertags/forms/search');

$subtypes = ubertags_get_enabled_subtypes();

if ($search = get_input('ubertags_search', NULL)) {
	foreach ($subtypes as $subtype) {
		echo elgg_view('ubertags/ubertags_generic_container', array('subtype' => $subtype, 'search' => $search));
	}
}

?>