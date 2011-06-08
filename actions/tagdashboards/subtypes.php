<?php
/**
 * Tag Dashboards admin enable entities action
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$enabled_subtypes = get_input('subtypes');

elgg_set_plugin_setting('enabled_subtypes', serialize($enabled_subtypes), 'tagdashboards');

system_message(elgg_echo('tagdashboards:success:setenabledsubtypes'));

forward(REFERER);
