<?php
/**
 * Tag Dashboards Blogs Content Teaser View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 *
 */

$blog = elgg_extract('entity', $vars, FALSE);

if (!$blog) {
	return TRUE;
}

$owner = $blog->getOwnerEntity();
$container = $blog->getContainerEntity();

$excerpt = $blog->excerpt;
if (!$excerpt) {
	$excerpt = elgg_get_excerpt($blog->description);
}

$author_text = elgg_echo('byline', array($owner_link));
$date = elgg_view_friendly_time($blog->time_created);

// The "on" status changes for comments, so best to check for !Off
if ($blog->comments_on != 'Off') {
	$comments_count = $blog->countComments();
	//only display if there are commments
	if ($comments_count != 0) {
		$text = elgg_echo("comments") . " ($comments_count)";
		$comments_link = elgg_view('output/url', array(
			'href' => $blog->getURL() . '#blog-comments',
			'text' => $text,
			'is_trusted' => true,
		));
	} else {
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

$subtitle = "$author_text $date $comments_link";

$params = array(
	'title' => elgg_view('output/url', array('text' => $blog->title, 'href' => $blog->getURL(), 'target' => '_blank')),
	'entity' => $blog,
	'subtitle' => $subtitle,
	'content' => $excerpt,
	'tags' => ' ',
);
$params = $params + $vars;
$list_body = elgg_view('object/elements/summary', $params);

$icon = tagdashboards_get_blog_preview_image($blog);

echo elgg_view_image_block($icon, $list_body);

