<?php
/**
 * Tag Dashboards group select ajax view
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * @uses $vars['value']
 */

$heading = elgg_view_title(elgg_echo('tagdashboards:label:selectgroup'));
$form =  elgg_view_form('tagdashboards/group_select');

echo $heading;
echo $form;