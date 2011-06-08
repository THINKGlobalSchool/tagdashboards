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

// Register tag dashboards JS library
$url = elgg_get_simplecache_url('js', 'tagdashboards');
elgg_register_js($url, 'tagdashboards');


$page_owner = elgg_get_page_owner_entity();
$page_owner_guid = $page_owner->getGUID();
$tag_portfolio = $page_owner->tag_portfolio;

echo <<<HTML
	<script type='text/javascript'>
	// Hack to switch the selected tab to the proper one..won't need this in 1.8
	$('div.profile > ul > li').removeClass('selected');
	$('li#tagportfolio-tab').addClass('selected');
	
	$(document).ready(function() {
		
		// Set up options
		var options = new Array();
		options['type'] = 'custom';
		options['custom_tags'] = '$tag_portfolio',
		options['owner_guids'] = new Array('$page_owner_guid');
		
		elgg.tagdashboards.display(options);
		
		$('#tagdashboards-refresh-input').click(function() {
			options['custom_tags'] = $('#tagdashboards-custom-input').val();
			
			elgg.tagdashboards.display(options);
			return false;
		});
	});
</script>
HTML;

echo elgg_view_title(elgg_echo('tagdashboards:title:tagportfolio'));

if (get_loggedin_userid() == $page_owner_guid) {
	echo elgg_view('forms/tagdashboards/tagportfolio', array('tag_portfolio' => $tag_portfolio));
} else {
	if (!$tag_portfolio) {
		echo elgg_echo('tagdashboards:error:noportfolio', array($page_owner->name));
	}
}

echo "<div class='tagdashboards-content-container' class='tagdashboards-left'></div>";