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
<div id="ubertag-timeline" class='dark-theme'></div>
<script>
	var json_data_url = "<?php echo $json_data_url; ?>";

	$(document).ready(function () {
		window.onload = onLoad;
		window.onresize = onResize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
	});

	// Override setBandShiftAndWidth to show bands at 100%
	Timeline._Band.prototype.setBandShiftAndWidth = function(shift, width) {
	    var inputDiv = this._keyboardInput.parentNode;
	    var middle = shift + Math.floor(width / 2);
	    if (this._timeline.isHorizontal()) {
	        this._div.style.top = shift + "px";
	        this._div.style.height = "100%";//width + "px"; Sets width to 100% for scrolling

	        inputDiv.style.top = middle + "px";
	        inputDiv.style.left = "-1em";
	    } else {
	        this._div.style.left = shift + "px";
	        this._div.style.width = width + "px";

	        inputDiv.style.left = middle + "px";
	        inputDiv.style.top = "-1em";
	    }
	};
	
	// Override to set a max height on bubbles
	Timeline.CompactEventPainter.prototype._showBubble = function(x, y, evts) {
	    var div = document.createElement("div");

	    evts = ("fillInfoBubble" in evts) ? [evts] : evts;
	    for (var i = 0; i < evts.length; i++) {
	        var div2 = document.createElement("div");
	        div.appendChild(div2);

	        evts[i].fillInfoBubble(div2, this._params.theme, this._band.getLabeller());
	    }

	    SimileAjax.WindowManager.cancelPopups();
	
		// Added 'left' (orientation) and 450 (max-height)
	    SimileAjax.Graphics.createBubbleForContentAndPoint(div, x, y, this._params.theme.event.bubble.width, 'left', 450);
	};
	

	 var tl;
        function onLoad() {
			// Which element will hold the timeline
            var tl_el = document.getElementById("ubertag-timeline");

			// Create an event source (will be a JSON feed)
            var eventSource = new Timeline.DefaultEventSource();
            
            var theme = Timeline.ClassicTheme.create();
			theme.mouseWheel = 'default';
			theme.event.instant.icon = "no-image-40.png";
			theme.event.instant.iconWidth = 15;  // These are for the default stand-alone icon
			theme.event.instant.iconHeight = 15;
			
                    
            var url = '.'; // The base url for image, icon and background image
			var date = '';

			Timeline.loadJSON(json_data_url, function(data, url){
				eventSource.loadJSON(data, url);
				date = eventSource.getLatestDate();
				
				// Going to set up all this stuff when the load is complete..
				var bandInfos = [
					Timeline.createBandInfo({
						date:  			date, 
						overview: 		true, 
			  			eventSource:    eventSource,
						width:          "15%", 
		         		intervalUnit:   Timeline.DateTime.MONTH, 
		         		intervalPixels: 100, 
		                theme:          theme,
						align: 			"Top",		// Align the dates to the top, easy way
		                layout:         'original'  // original, overview, detailed
			     	}), 
	                Timeline.createBandInfo({
						date:  			date,
						eventSource:    eventSource,
			  			width:          "85%", 
		         		intervalUnit:   Timeline.DateTime.DAY, 
		         		intervalPixels: 100, 
	                    theme:          theme,
						align: 			"Top", 	
	                    layout:         'original',  
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
			     	})
	            ];

				// Sync up the bands
				bandInfos[0].syncWith = 1;
				bandInfos[0].highlight = true;

	            // create the Timeline
	            tl = Timeline.create(tl_el, bandInfos, Timeline.HORIZONTAL);

	            tl.layout(); // display the Timeline
			});
		

			
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