<?php
/**
 * Tag Dashboards Timeline JS library
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2015
 * @link http://www.thinkglobalschool.org/
 */ 
?>
//<script>
elgg.provide('elgg.tagdashboards.timeline');

// Vars
elgg.tagdashboards.timeline.is_tl_loaded = false;
elgg.tagdashboards.timeline.guid;
elgg.tagdashboards.timeline.feedURL;
elgg.tagdashboards.timeline.element;
elgg.tagdashboards.timeline.resizeTimerID = null;

// Init function
elgg.tagdashboards.timeline.init = function () {
	// On resize event
	window.onresize = elgg.tagdashboards.timeline.on_resize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
	
	elgg.tagdashboards.timeline.guid = $('#tagdashboard-guid').val(); 
	elgg.tagdashboards.timeline.feedURL = elgg.get_site_url() + "tagdashboards/timelinefeed/" + elgg.tagdashboards.timeline.guid;
	
	// Check for timeline hash
	if (window.location.hash == '#timeline') {
		elgg.tagdashboards.timeline.toggle(1);
	}
}

elgg.tagdashboards.timeline.toggle_view = function(hook, type, params, value) {
	if (params.href === '#timeline') {
		elgg.tagdashboards.timeline.toggle(1);
	} else {
		elgg.tagdashboards.timeline.toggle(0);
	}
	return value;
}

elgg.tagdashboards.timeline.toggle = function(on) {
	if (Number(on)) {
		window.location.hash = "timeline";
		$("#tagdashboards-timeline-container").show();
		if (!elgg.tagdashboards.timeline.is_tl_loaded) {
			elgg.tagdashboards.timeline.init_timeline();
			elgg.tagdashboards.timeline.is_tl_loaded = true;
		}
	} else {	
		$("#tagdashboards-timeline-container").hide();
	}
}

elgg.tagdashboards.timeline.init_timeline = function() {
		
    elgg.tagdashboards.timeline.element = document.getElementById("tagdashboard-timeline");

	var detailedSource = new Timeline.DefaultEventSource(); // Detailed events

    var theme1 = Timeline.ClassicTheme.create();

    theme1.autoWidth = true; // Set the Timeline's "width" automatically.
                             // Set autoWidth on the Timeline's first band's theme,
                             // will affect all bands.


    // create a second theme for the second band because we want it to have different settings
    var theme2 = Timeline.ClassicTheme.create();

    // increase tape height
    theme2.event.tape.height = 6; // px
    theme2.event.track.height = theme2.event.tape.height + 6;

    var bandInfos = [
        Timeline.createBandInfo({
			overview: 		true,
            width:          50, // set to a minimum, autoWidth will then adjust
            intervalUnit:   Timeline.DateTime.MONTH, 
            intervalPixels: 500,
            eventSource:    detailedSource,
            theme:          theme1,
            layout:         'original',  // original, overview, detailed
			align: 			'Top' 
        }),
        Timeline.createBandInfo({
            width:          175, // set to a minimum, autoWidth will then adjust
            intervalUnit:   Timeline.DateTime.DAY, 
            intervalPixels: 100,
            eventSource:    detailedSource,
            theme:          theme2,
            layout:         'original',  // original, overview, detailed
			align: 			'Top',
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

	bandInfos[0].highlight = true;
    bandInfos[0].syncWith = 1;

	// create the Timeline
	elgg.tagdashboards.timeline.element = Timeline.create(elgg.tagdashboards.timeline.element, bandInfos, Timeline.HORIZONTAL);
	
	// Load data, overview first
	var feedURL = elgg.tagdashboards.timeline.feedURL+ "?type=detailed&timeline_override=1";
	
    elgg.tagdashboards.timeline.element.loadJSON(feedURL, function(json, url) {
		detailedSource.loadJSON(json, url);
		// Also (now that all events have been loaded), automatically re-size
		elgg.tagdashboards.timeline.element.finishedEventLoading(); // Automatically set new size of the div 
		elgg.tagdashboards.timeline.element.getBand(0).setCenterVisibleDate(detailedSource.getLatestDate());
		elgg.tagdashboards.timeline.element.getBand(1).setCenterVisibleDate(detailedSource.getLatestDate()); // Center the timline on last entry
	});
}

/**	
 * on resize function, required for timeline
 */ 
elgg.tagdashboards.timeline.on_resize = function() {
    if (elgg.tagdashboards.timeline.resizeTimerID == null) {
        elgg.tagdashboards.timeline.resizeTimerID = window.setTimeout(function() {
            elgg.tagdashboards.timeline.resizeTimerID = null;
			if (elgg.tagdashboards.timeline.element) {
				elgg.tagdashboards.timeline.element.layout();
			}
        }, 500);
    }
}

elgg.tagdashboards.timeline.initVideoLightbox = function(guid) {
	if (elgg.simplekaltura_utility) {
		$(".simplekaltura-lightbox-" + guid).colorbox(elgg.simplekaltura_utility.get_lightbox_init());
	}
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards.timeline.init);
elgg.register_hook_handler('toggle_view', 'tagdashboards', elgg.tagdashboards.timeline.toggle_view);