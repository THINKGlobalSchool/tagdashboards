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

	$owner = $video->getOwnerEntity();

	$pop_url = elgg_get_site_url() . 'videos/popup/' . $video->guid;
	$video_playlist[$idx]['title'] = $video->title;
	$video_playlist[$idx]['description'] = $owner->name;
	$video_playlist[$idx]['image'] = $video->getIconURL() . "/width/307/height/230";
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
				backgroundopacity: 0.5,
				wmode: 'transparent',
				showduration: true,
				width: '100%',
				height: '300px',
				playlist: playlist,
				coverwidth: 307,
				coverheight: 230,
				fixedsize: true,
				textoffset: 40,
				y: -10,
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
		setTimeout(function() {
			// Focus the middle item
			var mid = Math.ceil(playlist.length/2);
			coverflow().to(mid - 1);
		}, 1000
		);
	</script>
JAVASCRIPT;

	echo $script;
} else {
	$content = "<div style='width: 100%; text-align: center; margin: 10px;'><strong>No results</strong></div>";
	echo elgg_view_module('info', 'Videos', $content);
}
