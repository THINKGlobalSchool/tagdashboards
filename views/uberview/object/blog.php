<?php
/**
 * Tag Dashboards custom blog object
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

$blog = (isset($vars['entity'])) ? $vars['entity'] : FALSE;

if (!$blog) {
	return '';
}

$owner = get_entity($blog->owner_guid);
$container = get_entity($blog->container_guid);
$linked_title = "<a href=\"{$blog->getURL()}\" title=\"" . htmlentities($blog->title) . "\">{$blog->title}</a>";
$categories = elgg_view('categories/view', $vars, FALSE, FALSE, 'default');
$excerpt = $blog->excerpt;

$body = autop($blog->description);
$owner_icon = elgg_view('profile/icon', array('entity' => $owner, 'size' => 'tiny'), FALSE, FALSE, 'default');
$owner_blog_link = "<a href=\"".elgg_get_site_url()."pg/blog/$owner->username\">{$owner->name}</a>";
$author_text = elgg_echo('blog:author_by_line', array($owner_blog_link));
if($blog->tags){
	$tags = "<p class=\"tags\">" . elgg_view('output/tags', array('tags' => $blog->tags), FALSE, FALSE, 'default') . "</p>";
}else{
	$tags = "";
}
$date = elgg_view_friendly_time($blog->publish_date);

// The "on" status changes for comments, so best to check for !Off
if ($blog->comments_on != 'Off') {
	$comments_count = elgg_count_comments($blog);
	//only display if there are commments
	if($comments_count != 0){
		$comments_link = "<a href=\"{$blog->getURL()}#annotations\">" . elgg_echo("comments") . " (". $comments_count .")</a>";
	}else{
		$comments_link = '';
	}
} else {
	$comments_link = '';
}

echo <<<___END
<div class="blog $status_class entity_listing clearfix">
	<div class="entity_listing_icon">
		$owner_icon
	</div>
	<div class="entity_listing_info">
		<div class="entity_metadata" style='min-width: 0px;'>$edit</div>
		<p class="entity_title">$linked_title</p>
		<p class="entity_subtext">
			$author_text
			$date
			$categories
			$comments_link
		</p>
		$tags
		<p>$excerpt</p>
	</div>
</div>
___END;
?>
