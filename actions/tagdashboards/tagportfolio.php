<?php
/**
 * Tag Dashboards save user tag portfolio tags
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$user = elgg_get_logged_in_user_entity();
$custom = get_input('custom');
$user->tag_portfolio = $custom;

system_message(elgg_echo('tagdashboards:success:saveportfolio'));
forward(REFERER);