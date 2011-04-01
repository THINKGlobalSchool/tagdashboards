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

// Hack to switch the selected tab to the proper one..won't need this in 1.8
echo "<script type='text/javascript'>
	$('div.profile > ul > li').removeClass('selected');
	$('li#tagportfolio-tab').addClass('selected');
</script>";

