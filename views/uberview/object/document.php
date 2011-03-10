<?php
/**
 * Ubertags custom document object view
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */
global $CONFIG;

$file = $vars['entity'];
if ($file) {
	$file_guid = $file->getGUID();
	$tags = $file->tags;
	$title = $file->title;
	$desc = $file->description;
	$owner = $vars['entity']->getOwnerEntity();
	$friendlytime = friendly_time($vars['entity']->time_created);
	$file_type = $file->simpletype;
	$mime = $file->mimetype;

	$info .= "<p class='entity_title'><a href=\"{$file->getURL()}\">{$title}</a></p>";

	$info .= "<p class='entity_subtext'><a href=\"{$vars['url']}pg/document/{$owner->username}\">{$owner->name}</a> {$friendlytime}";
	// get the number of comments
	$numcomments = elgg_count_comments($file);
	if ($numcomments) {
		$info .= ", <a href=\"{$file->getURL()}\">" . sprintf(elgg_echo("comments")) . " (" . $numcomments . ")</a>";
	}
	$info .= "</p>";
	$icon = elgg_view("profile/icon",array('entity' => $owner, 'size' => 'tiny'));


	//display
	echo elgg_view_listing($icon, $info);

}else {	
	echo "<p class='margin_top'>".elgg_echo('document:none')."</p>";
}