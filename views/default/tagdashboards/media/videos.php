<?php
/**
 * Tag Dashboards Videos Media View
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2013
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['dashboard_guid']
 */

$dashboard_guid = elgg_extract('dashboard_guid', $vars);

$videos = new ElggBatch('elgg_get_entities_from_metadata', tagdashboards_get_media_entities_options($dashboard_guid, array(
	'subtype' => 'simplekaltura_video'
)));


foreach ($videos as $idx => $video) {
	// Get entry info
	$entry_info = unserialize($video->raw_entry);
	
	if ($entry_info->status == -1) {
		continue;
	}

	$pop_url = elgg_get_site_url() . 'videos/popup/' . $video->guid;
	$video_playlist[$idx]['title'] = $video->title;
	$video_playlist[$idx]['image'] = $video->getIconURL() . "/width/200/height/150";
	$video_playlist[$idx]['link'] = $pop_url;
	$video_playlist[$idx]['duration'] = $video->duration;
}

if (count($video_playlist)) {
	$coverflow_container = "<div id='tagdashboards-media-videos-coverflow'></div>";

	echo elgg_view_module('info', 'Videos', $coverflow_container, array(
		'class' => 'tagdashboards-media-videos-container'
	));

	$playlist = json_encode($video_playlist);

	$script = <<<JAVASCRIPT
	<script type='text/javascript'>
		var playlist = $playlist;
		coverflow('tagdashboards-media-videos-coverflow').setup({
				item: 0,
				backgroundcolor: '000000',
				backgroundopacity: 0.7,
				wmode: 'transparent',
				showduration: true,
				width: '100%',
				height: '300px',
				playlist: playlist,
				coverwidth: 266,
				coverheight: 200,
				fixedsize: true,
				textoffset: 50, 
			}).on('ready', function() {
				// Focus
				this.on('focus', function(index) {});

				// Click event
				this.on('click', function(index, link) {
					var lightbox = $(document.createElement('a'));
					lightbox.attr('href', link);
					lightbox.fancybox(elgg.simplekaltura_utility.get_lightbox_init()).trigger('click');
				});
			});
		setTimeout(function() {coverflow().to(0);}, 1000);
	</script>
JAVASCRIPT;

	echo $script;
} else {
	echo elgg_view_module('info', 'Videos', "No results");
}
