var json_data_url = "";

$(document).ready(function () {
	window.onload = onLoad;
	window.onresize = onResize; // Can't do this with jQuery for some reason... ie: $("body").resize(...)
});

function set_timeline_data_url(url) {
	json_data_url = url;
}


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


var tl;
function onLoad() {
    var tl_el = document.getElementById("ubertag-timeline-new");
    var eventSource1 = new Timeline.DefaultEventSource();
    var eventSource2 = new Timeline.DefaultEventSource();

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
			overview: 		true,
            width:          50, // set to a minimum, autoWidth will then adjust
            intervalUnit:   Timeline.DateTime.MONTH, 
            intervalPixels: 100,
            eventSource:    eventSource1,
            theme:          theme1,
            layout:         'original',  // original, overview, detailed
			align: 			'Top' 
        }),
        Timeline.createBandInfo({
            width:          175, // set to a minimum, autoWidth will then adjust
            intervalUnit:   Timeline.DateTime.DAY, 
            intervalPixels: 100,
            eventSource:    eventSource1,
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

	// Asynchronous Callback functions. Called after data arrives.
	function load_json1(json, url) {
		// Called with first json file from server
		// Also initiates loading of second Band

		eventSource1.loadJSON(json, url);
		// Also (now that all events have been loaded), automatically re-size
		tl.finishedEventLoading(); // Automatically set new size of the div 
		
		tl.getBand(0).setCenterVisibleDate(eventSource1.getLatestDate());
		tl.getBand(1).setCenterVisibleDate(eventSource1.getLatestDate()); // Center the timline on last entry
	}

	// create the Timeline
	// Strategy: Initiate Ajax call for first band's data, then have its callback
	// initiate Ajax call for second band's data. Then have its callback 
	// automagically resize the overall Timeline since we will then have all
	// the data.
	tl = Timeline.create(tl_el, bandInfos, Timeline.HORIZONTAL);

	// Load data
	tl.loadJSON(json_data_url, load_json1);
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