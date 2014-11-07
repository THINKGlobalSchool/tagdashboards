<?php
/**
 * Tag Dashboards add tag form
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2014
 * @link http://www.thinkglobalschool.com/
 * 
 */

$entity = elgg_extract('entity', $vars);

$add_tag_input = elgg_view('input/text', array(
	'name' => 'tag',
));

$submit_input = elgg_view('input/submit', array(
	'name' => 'submit', 
	'value' => elgg_echo('tagdashboards:label:add'),
	'class' => 'elgg-button elgg-button-action td-add-tag-submit',
	'id' => 'td-add-tag-submit-' . $entity->guid,
));

$entity_hidden = elgg_view('input/hidden', array(
	'name' => 'entity_guid',
	'value' => $entity->guid,
));

$content = <<<HTML
	<div>
		$add_tag_input
	</div>
	<div class='elgg-foot'>
		$submit_input
		$entity_hidden
	</div>
HTML;

echo $content;