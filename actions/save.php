<?php
/**
 * Ubertags admin enable entities action
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
gatekeeper();

// Get inputs
$title = get_input('ubertag_title');
$description = get_input('ubertag_description');
$tags = string_to_tag_array(get_input('ubertag_tags'));
$access = get_input('ubertag_access');
$search = get_input('ubertag_search');

//@TODO Check values..

$ubertag = new ElggObject();
$ubertag->subtype = 'ubertag';
$ubertag->title = $title;
$ubertag->description = $description;
$ubertag->tags = $tags;
$ubertag->access_id = $access;
$ubertag->search = $search;

// If error saving, register error and return
if (!$ubertag->save()) {
	register_error(elgg_echo('ubertags:error:save'));
	forward(REFERER);
}

// Add to river
add_to_river('river/object/ubertag/create', 'create', get_loggedin_userid(), $ubertag->getGUID());

// Forward on
system_message(elgg_echo('ubertags:success:save'));
forward('pg/ubertags');


?>