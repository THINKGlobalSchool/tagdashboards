<?php
/**
 * Ubertags group by user defined, based on search tag container
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$custom = $vars['custom'];
$search = $vars['search'];
$subtypes = $vars['subtypes'];

$content .= "<div class='ubertag-big-title'>
				$search
			</div>";

foreach($custom as $tag) {
	$content .= elgg_view('ubertags/custom_container', array(
		'search' => $search, 
		'group' => $tag, 
		'subtypes' => $subtypes,
	));
}

echo $content . "<div style='clear: both;'></div>";