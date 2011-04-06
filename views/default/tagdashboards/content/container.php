<?php
/**
 * Tag Dashboards content container
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @uses $vars['heading']  - Heading to display
 * @uses $vars['id'] - Identifier
 * @uses $vars['container_class'] - Container class
 */

// Get values
$heading 	= $vars['heading'];
$class 		= $vars['container_class'];
$id 		= $vars['id'];

$content_id = $id . "_content";

// Create spinner
$spinner = elgg_view('tagdashboards/ajax_spinner', array('id' => 'loading_' . $id));

// Build content
$content .= <<<HTML
<div class='tagdashboards-container $class'>
	<h3 class='tagdashboards-container-title'>$heading</h3>
		$spinner
	<div id='$content_id'>
	</div>
</div>
HTML;

// Dump content
echo $content;