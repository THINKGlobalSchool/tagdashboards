<?php
/**
 * Timeline view for blogs
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$comments_count = elgg_count_comments($vars['entity']);
$likes_count = elgg_count_likes($vars['entity']);

echo "<div class='entity_subtext timeline-entity-subtext'>
		Likes: $likes_count $views_string Comments: $comments_count
	</div>". elgg_get_excerpt($vars['entity']->description);
?>