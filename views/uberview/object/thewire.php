<?php
/**
 * Tag Dashboards custom wire object view
 *
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */

if (isset($vars['entity'])) {
	$user_name = $vars['entity']->getOwnerEntity()->name;
	
	//if the note is a reply, we need some more info
	$note_url = '';
	$note_owner = elgg_echo("thewire:notedeleted");  	
	
	
	$icon = elgg_view("profile/icon",array('entity' => $vars['entity']->getOwnerEntity(), 'size' => 'tiny'), false, false, 'default');
	
	$desc = $vars['entity']->description;
	//$desc = preg_replace('/\@([A-Za-z0-9\_\.\-]*)/i','@<a href="' . $vars['url'] . 'pg/thewire/$1">$1</a>',$desc);
	
	// Strip out hashtags
	$regex = '/#([A-Aa-z0-9_-]+)/is';
	$desc = (preg_replace($regex, '', $desc));

	$info = parse_urls($desc);
		



	echo <<<___END
	<div class='entity_listing clearfix'>
		<div class="entity_listing_icon">
			$icon
		</div>
		<div class="entity_listing_info">
			$info
		</div>
	</div>
___END;
}
