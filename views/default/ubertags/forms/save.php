<?php
/**
 * Ubertags save form
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

// Labels/Inputs
$title_label = elgg_echo('ubertags:label:title');
$title_input = elgg_view('input/text', array(
	'internalid' => 'ubertag_title',
	'internalname' => 'ubertag_title'
));

$description_label =  elgg_echo('ubertags:label:description');
$description_input = elgg_view('input/longtext', array(
	'internalid' => 'ubertag_description',
	'internalname' => 'ubertag_description'
));

$tags_label =  elgg_echo('ubertags:label:tags');
$tags_input = elgg_view('input/tags', array(
	'internalid' => 'ubertag_tags',
	'internalname' => 'ubertag_tags'
));

$access_label =  elgg_echo('access');
$access_input = elgg_view('input/access', array(
	'internalid' => 'ubertag_access',
	'internalname' => 'ubertag_access'
));

// Hidden search input
$search_input = elgg_view('input/hidden', array(
	'internalid' => 'ubertag_search',
	'internalname' => 'ubertag_search',
	'value' => $vars['search']
));

$ubertags_save_input = elgg_view('input/submit', array(
	'internalid' => 'ubertags_save_input',
	'internalname' => 'ubertags_save_input',
	'value' => elgg_echo('ubertags:label:save')
));

$form_body = <<<EOT
	<div id='ubertags_save'>
		<p>
			<label>$title_label</label>
			$title_input
		</p>
		<p>
			<label>$description_label</label>
			$description_input
		</p>
		<p>
			<label>$tags_label</label>
			$tags_input
		</p>
		<br />
		<p>
			<label>$access_label</label>
			$access_input
		</p>
		<p>
			$ubertags_save_input
			$search_input
		</p>
	</div>
EOT;

echo elgg_view('input/form', array(
	'body' => $form_body,
	'action' => 'action/ubertags/save'
));
