<?php
/**
 * Tag Dashboards Timeline JS library
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */ 
?>
//<script>
elgg.provide('elgg.tagdashboards.timeline');

// Globals needed for local timeline code
//Timeline_ajax_url = elgg.get_site_url() + 'mod/tagdashboards/vendors/timeline_2.3.1/ajax/api/simile-ajax-api.js';
//Timeline_urlPrefix = elgg.get_site_url() + 'mod/tagdashboards/vendors/timeline_2.3.1/webapp/api/'; 

Timeline_ajax_url = elgg.get_site_url() + 'mod/tagdashboards/vendors/timeline_lib/timeline_ajax/simile-ajax-api.js';
Timeline_urlPrefix = elgg.get_site_url() + 'mod/tagdashboards/vendors/timeline_lib/timeline_js/';    
Timeline_parameters='bundle=true';  

// Vars
elgg.tagdashboards.timeline.is_tl_loaded = false;
elgg.tagdashboards.timeline.guid;;
elgg.tagdashboards.timeline.feedURL;
elgg.tagdashboards.timeline.element;
elgg.tagdashboards.timeline.resizeTimerID = null;

// Init function
elgg.tagdashboards.timeline.init = function () {
	// On resize event
	window.onresize = elgg.tagdashboards.timeline.on_resize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
	
	elgg.tagdashboards.timeline.guid = $('#tagdashboard-guid').val(); 
	elgg.tagdashboards.timeline.feedURL = elgg.get_site_url() + "tagdashboards/timelinefeed/" + elgg.tagdashboards.timeline.guid;
	
	// Handle click for timeline button
	$('.switch-tagdashboards').live('click', function(event) {
		elgg.tagdashboards.timeline.toggle($(this).attr('href'));
		event.preventDefault();
	});
}

elgg.tagdashboards.timeline.toggle = function(on) {
	if (Number(on)) {
		window.location.hash = "timeline";
		$("#tagdashboards-timeline-container").show();
		$(".tagdashboard-container").hide();
		if (!elgg.tagdashboards.timeline.is_tl_loaded) {
			elgg.tagdashboards.timeline.init_timeline();
			elgg.tagdashboards.timeline.is_tl_loaded = true;
		}
	} else {
		window.location.hash = "";	
		$("#tagdashboards-timeline-container").hide();
		$(".tagdashboard-container").show();
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

/**
 * Creates a popup out of a dialog with given id 
 */
elgg.tagdashboards.timeline.timeline_create_popup_with_id = function (id, width) {
	if (!width) {
		width = 'auto';
	}
	
	elgg.tagdashboards.timeline.dlg = $("#" + id).dialog({
						autoOpen: false,
						width: width, 
						height: 'auto',
						modal: true,
						open: function(event, ui) { 
							$(".ui-dialog-titlebar-close").hide(); 	
						},
						buttons: {
							"X": function() { 
								$(this).dialog("close"); 
							} 
	}});
}

/**
 * Loads a popup with given url *
 */
elgg.tagdashboards.timeline.timeline_load_popup_by_id = function (id, load_url) {
	elgg.tagdashboards.timeline.timeline_create_popup_with_id(id, 500);
	$("#" + id).dialog("open");
	$("#" + id).load(load_url);
}

/**
 * Creates an image popup with given src 
 */
elgg.tagdashboards.timeline.timeline_show_image_popup_by_id = function (id, src) {
	elgg.tagdashboards.timeline.timeline_create_popup_with_id(id, 640);
	$("#" + id).dialog("open");
	$("#" + id).html("<img src='" + src + "' />");
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards.timeline.init);
//</script>