var json_data_url = "";
var latest_date = "";

// Set up some handlers
$(document).ready(function () {
	window.onload = onLoad;
	window.onresize = onResize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
	
	// This will set/unset a flag so the timeline only gets reloaded when the mouse is released from the id
	$("#ubertag-timeline").mouseup(function(){
		mouseUp = true;
		displaymsg("Mouse up!");
	}).mousedown(function(){
		mouseUp = false;
		displaymsg("Mouse down!");
	});
});

/** TIMELINE HACKS **/

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

// This little function was responsible for '[space]' showing up everywhere
SimileAjax.HTML.deEntify = function(s) {
	// don't do anything.. just return whatever was passed in
    return s;
};

// This function doesn't exist for some reason in the compact painter
Timeline.CompactEventPainter.prototype.getType = function() {
    return 'compact'; // just needs to announce its type..
};

// Autowidth support hacks
Timeline._Band.prototype.checkAutoWidth = function() {
    // if a new (larger) width is needed by the band
    // then: a) updates the band's bandInfo.width
    //
    // desiredWidth for the band is 
    //   (number of tracks + margin) * track increment
    if (! this._timeline.autoWidth) {
      return; // early return
    }

    var overviewBand = this._eventPainter.getType() == 'overview';
    var margin = overviewBand ? 
       this._theme.event.overviewTrack.autoWidthMargin : 
       this._theme.event.track.autoWidthMargin;

    var desiredWidth = Math.ceil((this._eventTracksNeeded + margin) * this._eventTrackIncrement);


    // add offset amount (additional margin)
    desiredWidth += overviewBand ? this._theme.event.overviewTrack.offset: 
                                   this._theme.event.track.offset;

    var bandInfo = this._bandInfo;

    if (desiredWidth > bandInfo.width) { // used to be desiredWidth != bandInfo.width, now it will only resize larger
 		bandInfo.width = desiredWidth;
    }
};

// Overridden CompactEventPainter paint function to force it to work with autowidth
Timeline.CompactEventPainter.prototype.paint = function() {
    var eventSource = this._band.getEventSource();
    if (eventSource == null) {
        return;
    }

    this._eventIdToElmt = {};
    this._prepareForPainting();

    var theme = this._params.theme;
    var eventTheme = theme.event;

    var metrics = {
        trackOffset:            "trackOffset" in this._params ? this._params.trackOffset : 10,
        trackHeight:            "trackHeight" in this._params ? this._params.trackHeight : 10,

        tapeHeight:             theme.event.tape.height,
        tapeBottomMargin:       "tapeBottomMargin" in this._params ? this._params.tapeBottomMargin : 2,

        labelBottomMargin:      "labelBottomMargin" in this._params ? this._params.labelBottomMargin : 5,
        labelRightMargin:       "labelRightMargin" in this._params ? this._params.labelRightMargin : 5,

        defaultIcon:            eventTheme.instant.icon,
        defaultIconWidth:       eventTheme.instant.iconWidth,
        defaultIconHeight:      eventTheme.instant.iconHeight,

        customIconWidth:        "iconWidth" in this._params ? this._params.iconWidth : eventTheme.instant.iconWidth,
        customIconHeight:       "iconHeight" in this._params ? this._params.iconHeight : eventTheme.instant.iconHeight,

        iconLabelGap:           "iconLabelGap" in this._params ? this._params.iconLabelGap : 2,
        iconBottomMargin:       "iconBottomMargin" in this._params ? this._params.iconBottomMargin : 2
    };
    if ("compositeIcon" in this._params) {
        metrics.compositeIcon = this._params.compositeIcon;
        metrics.compositeIconWidth = this._params.compositeIconWidth || metrics.customIconWidth;
        metrics.compositeIconHeight = this._params.compositeIconHeight || metrics.customIconHeight;
    } else {
        metrics.compositeIcon = metrics.defaultIcon;
        metrics.compositeIconWidth = metrics.defaultIconWidth;
        metrics.compositeIconHeight = metrics.defaultIconHeight;
    }
    metrics.defaultStackIcon = "icon" in this._params.stackConcurrentPreciseInstantEvents ?
        this._params.stackConcurrentPreciseInstantEvents.icon : metrics.defaultIcon;
    metrics.defaultStackIconWidth = "iconWidth" in this._params.stackConcurrentPreciseInstantEvents ?
        this._params.stackConcurrentPreciseInstantEvents.iconWidth : metrics.defaultIconWidth;
    metrics.defaultStackIconHeight = "iconHeight" in this._params.stackConcurrentPreciseInstantEvents ?
        this._params.stackConcurrentPreciseInstantEvents.iconHeight : metrics.defaultIconHeight;

    var minDate = this._band.getMinDate();
    var maxDate = this._band.getMaxDate();

    var filterMatcher = (this._filterMatcher != null) ? 
        this._filterMatcher :
        function(evt) { return true; };

    var highlightMatcher = (this._highlightMatcher != null) ? 
        this._highlightMatcher :
        function(evt) { return -1; };

    var iterator = eventSource.getEventIterator(minDate, maxDate);

    var stackConcurrentPreciseInstantEvents = "stackConcurrentPreciseInstantEvents" in this._params && typeof this._params.stackConcurrentPreciseInstantEvents == "object";
    var collapseConcurrentPreciseInstantEvents = "collapseConcurrentPreciseInstantEvents" in this._params && this._params.collapseConcurrentPreciseInstantEvents;
    if (collapseConcurrentPreciseInstantEvents || stackConcurrentPreciseInstantEvents) {
        var bufferedEvents = [];
        var previousInstantEvent = null;

        while (iterator.hasNext()) {
            var evt = iterator.next();
            if (filterMatcher(evt)) {
                if (!evt.isInstant() || evt.isImprecise()) {
                    this.paintEvent(evt, metrics, this._params.theme, highlightMatcher(evt));
                } else if (previousInstantEvent != null &&
                        previousInstantEvent.getStart().getTime() == evt.getStart().getTime()) {
                    bufferedEvents[bufferedEvents.length - 1].push(evt);
                } else {
                    bufferedEvents.push([ evt ]);
                    previousInstantEvent = evt;
                }
            }
        }

        for (var i = 0; i < bufferedEvents.length; i++) {
            var compositeEvents = bufferedEvents[i];
            if (compositeEvents.length == 1) {
                this.paintEvent(compositeEvents[0], metrics, this._params.theme, highlightMatcher(evt)); 
            } else {
                var match = -1;
                for (var j = 0; match < 0 && j < compositeEvents.length; j++) {
                    match = highlightMatcher(compositeEvents[j]);
                }

                if (stackConcurrentPreciseInstantEvents) {
                    this.paintStackedPreciseInstantEvents(compositeEvents, metrics, this._params.theme, match);
                } else {
                    this.paintCompositePreciseInstantEvents(compositeEvents, metrics, this._params.theme, match);
                }
            }
        }
    } else {
        while (iterator.hasNext()) {
            var evt = iterator.next();
            if (filterMatcher(evt)) {
                this.paintEvent(evt, metrics, this._params.theme, highlightMatcher(evt));
            }
        }
    }

    this._highlightLayer.style.display = "block";
    this._lineLayer.style.display = "block";
    this._eventLayer.style.display = "block";
	// update the band object for max number of tracks in this section of the ether
    this._band.updateEventTrackInfo(this._tracks.length, metrics.trackHeight); // This makes the autowidth work!
};

/** TIMELINE GUTS **/

var tl;
var limits_changed = false;
var refreshRateLoad = 2; // seconds
var loadIntervalId = 0;
var resizeTimerID = null;
var mouseUp = true;
var initialLoad = true;
var isLoading = false;

// Some magic
var limitsLoaded = new Array();

function onLoad() {
    var tl_el = document.getElementById("ubertag-timeline");
	tl = tl_el;
	var detailedSource = new Timeline.DefaultEventSource(); // Detailed events

    var theme1 = Timeline.ClassicTheme.create();
    theme1.autoWidth = true; // Set the Timeline's "width" automatically.
                             // Set autoWidth on the Timeline's first band's theme,
                             // will affect all bands.


    // create a second theme for the second band because we want it to
    // have different settings
    var theme2 = Timeline.ClassicTheme.create();

    // increase tape height
    theme2.event.tape.height = 6; // px
    theme2.event.track.height = theme2.event.tape.height + 6;

    var bandInfos = [
        Timeline.createBandInfo({
			date: 			latest_date,
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
			date: 			latest_date,
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
	tl = Timeline.create(tl_el, bandInfos, Timeline.HORIZONTAL);

	// This triggers the initial load	
	limits_changed = true;

	clearInterval (loadIntervalId);
	loadIntervalId = window.setInterval(function(){ reloadTimeline(detailedSource) }, refreshRateLoad * 1000);

	// change the flag so we know the timeline has moved
	tl.getBand(0).addOnScrollListener(function(band) {
    	limits_changed = true;
	});

}

function reloadTimeline(eventSource) {
	if ((limits_changed) && (mouseUp) && (!isLoading)) {
		isLoading = true; // Loads can take a while, so set a flag
		
		var band = tl.getBand(0);
		var min = Math.round(band.getMinVisibleDate().getTime() / 1000); 
		var max = Math.round(band.getMaxVisibleDate().getTime() / 1000); 

		if (initialLoad) {
			console.log('intitial');
			newLimits = new Array();
			newLimits['max'] = limitsLoaded['max'] = max;
			newLimits['min'] = limitsLoaded['min'] = min;
		} else {
			newLimits = getNewLimits(min, max);	
			if (!newLimits) {
				limits_changed = false;
				isLoading = false;
				return;
			}
		}
		
		if (!limitsLoaded['min'] || min < limitsLoaded['min']) {
			limitsLoaded['min'] = min;
		}

		if (!limitsLoaded['max'] || max > limitsLoaded['max']) {
			limitsLoaded['max'] = max;
		}

		// Clear the line
		displaymsg("Reloading, limits changed: " + limits_changed);
    	var data_url = json_data_url+"?type=detailed&min=" + newLimits['min'] + "&max=" + newLimits['max'];
		
   		tl.loadJSON(data_url, function(json, url) {	
			console.log('loading');
			eventSource.loadJSON(json, url);
			
			// Also (now that all events have been loaded), automatically re-size
			tl.finishedEventLoading(); // Automatically set new size of the div 
			
			if (initialLoad) {
				initialLoad = false;
				//tl.getBand(1).setCenterVisibleDate(eventSource.getLatestDate()); // Center the timline on last entry
			}	
					
			// You need to do this here cos layout triggers a loop on this flag
			limits_changed = false;
			isLoading = false;
     	});
	}
}

/** MISC HELPERS **/
function getNewLimits(min, max) {
	newLimits = new Array();
	
	//console.log("pmin: " + min);
	//console.log("pmax: " + max);
	
	// If limits lay between the already loaded limits
	if (limitsLoaded['min'] < min && limitsLoaded['max'] > max) {
		return false; // Do nothing, already loaded this stuff!
	}
	
	// If the old minimum is less than the new minimum
	if (limitsLoaded['min'] < min) {
		//console.log('min');
		newLimits['min'] = limitsLoaded['max']; 
		newLimits['max'] = max;
	} 
	
	// If the old maximum is greater than the new maximum
	if (limitsLoaded['max'] > max) {
		//console.log('max');
		newLimits['min'] = min;
		newLimits['max'] = limitsLoaded['min'];
	} 
	
	//console.log("llmin: " + limitsLoaded['min']);
	//console.log("llmax: " + limitsLoaded['max']);
	//console.log("nlmin: " + newLimits['min']);
	//console.log("nlmax: " + newLimits['max']);
	
	return newLimits;
}


// Set out data url
function setTimelineDataURL(url) {
	json_data_url = url;
}

// Set latest date
function setLatestDate(date) {
	latest_date = date;
}

function onResize() {
    if (resizeTimerID == null) {
        resizeTimerID = window.setTimeout(function() {
            resizeTimerID = null;
			if (tl) {
				tl.layout();
			}
        }, 500);
    }
}

function displaymsg(message){
	if(message.length > 0) {
		document.getElementById("info").innerHTML = message;
	}
}

function centerTimelineNow() {
    tl.getBand(0).setCenterVisibleDate(new Date().getTime());
}

function centerTimelineOffset(offset) {
	// Offset in days
	var centerdate = tl.getBand(2).getCenterVisibleDate();
	centerdate.setDate(centerdate.getDate()+offset)
	tl.getBand(0).setCenterVisibleDate(centerdate);
}