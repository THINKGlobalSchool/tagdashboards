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

$comments_count = elgg_count_comments($vars['entity']);
$comments_link = "<a href=\"#annotations\">" . elgg_echo('ubertags:label:leaveacomment') . elgg_echo("comments") . " (". $comments_count .")</a>";

if ($vars['entity']->canEdit()) {
	$edit_url = elgg_get_site_url()."pg/ubertags/edit/{$vars['entity']->getGUID()}/";
	$edit_link = "<span class='entity_edit'><a href=\"$edit_url\">" . elgg_echo('edit') . '</a></span> / ';
}

$content = "<div class='ubertag_big_title'>" . $vars['entity']->title . "</div>";
$content .= "<div class='ubertag_description'>" . $vars['entity']->description . "</div>";
$content .= "<div class='ubertag_comment_block'>" . $edit_link . $comments_link . "</div><div style='clear:both;'></div>";

$content .= "<div>" . elgg_view('ubertags/ubertags_list_results', array('search' => $vars['entity']->search, 'subtypes' => $vars['entity']->subtypes)) . "</div>";
$content .= "<a name='annotations'></a><hr style='border: 1px solid #bbb' />";
echo $content;
?>