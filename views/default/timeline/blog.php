<?php
/**
 * Timeline view for blogs
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$comments_count = $vars['entity']->countComments();
$likes_count = likes_count($vars['entity']);

echo "<div class='elgg-subtext timeline-entity-subtext'>
		Likes: $likes_count $views_string Comments: $comments_count
	</div>". elgg_get_excerpt(elgg_view('output/longtext', array('value' => $vars['entity']->description))) .
	 "<br /><a href='" . $vars['entity']->getURL() . "'><i>" . elgg_echo('tagdashboards:label:viewfull') . "</i></a>";
