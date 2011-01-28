<?php
/**
 * Ubertags timeline view
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$url = elgg_get_site_url() . 'mod/ubertags/vendors/timeline_230/timeline_js/timeline-api.js';

$json_data_url = elgg_get_site_url() . "pg/ubertags/timeline_feed/{$vars['entity']->getGUID()}";

// Register JS 
// HAVE TO HAVE TO HAVE TO HAVE TO LOAD THE JS IN THE HEAD!!! DUHHHHHH Wasted so much time on this.. its not even funny..
elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=true", 'timeline');

?>
<div id="ubertag-timeline"></div>
<script>
	var json_data_url = "<?php echo $json_data_url; ?>";

	$(document).ready(function () {
		window.onload = onLoad;
		window.onresize = onResize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
	});

	 var tl;
        function onLoad() {
			// Which element will hold the timeline
            var tl_el = document.getElementById("ubertag-timeline");

			// Create an event source (will be a JSON feed)
            var eventSource = new Timeline.DefaultEventSource();
            
            var theme = Timeline.ClassicTheme.create();
			//theme.mouseWheel = 'default';
			theme.event.instant.icon = "no-image-40.png";
			theme.event.instant.iconWidth = 15;  // These are for the default stand-alone icon
			theme.event.instant.iconHeight = 15;
			

           
            var bandInfos = [
                Timeline.createBandInfo({
					eventSource:    eventSource,
		  			width:          "70%", 
	         		intervalUnit:   Timeline.DateTime.DAY, 
	         		intervalPixels: 100, 
                    theme:          theme,
                    layout:         'original',  // original, overview, detailed
					eventPainter:   Timeline.CompactEventPainter,
					eventPainterParams: {
					                        iconLabelGap:     2,
					                        labelRightMargin: 0,

					                        iconWidth:        15, // These are for per-event custom icons
					                        iconHeight:       15,

					                        stackConcurrentPreciseInstantEvents: {
					                            limit: 10,
					                            moreMessageTemplate:    "%0 More Events",
					                            icon:                   "no-image-80.png", // default icon in stacks
					                            iconWidth:              15,
					                            iconHeight:             15
					                        }
					                    }
		     	}),
				Timeline.createBandInfo({
					overview: 		true, 
		  			eventSource:    eventSource,
					width:          "30%", 
	         		intervalUnit:   Timeline.DateTime.MONTH, 
	         		intervalPixels: 100, 
                    theme:          theme,
                    layout:         'original'  // original, overview, detailed
		     	})
            ];
			
			bandInfos[1].syncWith = 0;
			bandInfos[1].highlight = true;
                                                            
            // create the Timeline
            tl = Timeline.create(tl_el, bandInfos, Timeline.HORIZONTAL);
            
            var url = '.'; // The base url for image, icon and background image
                           // references in the data

			Timeline.loadJSON(json_data_url, function(data, url){
				eventSource.loadJSON(data, url);
			});
			
            tl.layout(); // display the Timeline
        }
        
        var resizeTimerID = null;
        function onResize() {
            if (resizeTimerID == null) {
                resizeTimerID = window.setTimeout(function() {
                    resizeTimerID = null;
                    tl.layout();
                }, 500);
            }
        }
	
</script>