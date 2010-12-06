<?php
/**
 * Ubertag entity full view
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$content = "<div class='ubertag_big_title'>" . $vars['entity']->title . "</div><div style='clear:both;'></div>";

$content .= "<div>" . elgg_view('ubertags/ubertags_list_results', array('search' => $vars['entity']->search, 'subtypes' => $entity->subtypes)) . "</div>";
echo $content;
?>