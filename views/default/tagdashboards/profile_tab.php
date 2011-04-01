<?php
/**
 * Tag Dashboards profile tab
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$url = $vars['user']->getURL();

?>
<li id='tagportfolio-tab' <?php echo $tagdashboards ?>><a href="<?php echo  $url . '/tagportfolio'; ?>"><?php echo elgg_echo('tagdashboards:label:tagportfolio'); ?></a></li>