<?php
/**
 * Ubertags search form
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$search_input = elgg_view('input/text', array(	
	'internalname' => 'ubertags_search', 
	'internalid' => 'ubertags_search_input',
	'value' => get_input('ubertags_search')
));

$search_submit = elgg_view('input/submit', array(
	'internalname' => 'ubertags_search_submit',
	'internalid' => 'ubertags_search_submit',
	'value' => elgg_echo('ubertags:label:submitsearch')
));

$form_body = <<<EOT
	<div>	
		$search_input $search_submit
	</div>
EOT;

echo elgg_view('input/form', array(
	'action' => elgg_get_site_url() . 'pg/ubertags/search', 
	'body' => $form_body, 
	'internalid' => 'ubertag_post_form',
	'method' => 'GET'
));