<?php
/**
 * Tag Dashboards CSS
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */
?>

#tagdashboards-timeline-container {
	display: none;
}

.tagdashboards-text-input {
	width: 80%;
	margin-top: 10px;
	display: inline;
	font-size: 1em;
}

span#tagdashboards-search-error {
	color: Red;
	font-size: 12px;
	font-weight: bold;
}

.tagdashboard-module {
	float: left;
	width: 47%;
	margin: 4px;
}

div.no-float .tagdashboard-module {
	width: auto;
	float: none;
}

.tagdashboards-groupby-div {
	display: none;
}

.tagdashboards-groupby-description {
	border: 1px solid #888;
	background: #ddd;
	padding: 15px;
	margin-top: 10px;
	margin-bottom: 10px;
}

div.tagdashboard-description {
	margin-bottom: 10px;
	color: #444;
	font-weight: bold;
}

div.tagdashboard-view-block {
	width: 200px;
	margin-left: auto;
	margin-right: auto;
	font-size: 11px;
	color: #666;
	text-align: center;
}

.enabled-content-type {
	float: left;
	margin-right: 12px;
	padding: 5px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	background: #cccccc;
	cursor: pointer;
}

.enabled-content-type label{
	margin-right: 4px;
	font-weight: bold;
}

.tagdashboards-groupby-radio li {
	margin-right: 12px;
	padding: 5px;
	-webkit-border-radius: 4px;
	-moz-border-radius: 4px;
	background: #cccccc;
	cursor: pointer;	
}

.tagdashboards-groupby-radio li label {
	cursor: pointer;
}

.tagdashboards-groupby-radio li label input {
	margin-right: 6px;
}

a.switch-tagdashboards {
	cursor: pointer;
}

a.tagdashboards-toggler {
	font-weight: bold;	
	font-size: 110%;
}

#tagdashboard-timeline-wrapper {
	max-height: 475px;
	overflow-y: scroll;
}

#tagdashboard-timeline {
	height: 200px;
	border: 1px solid #aaa;
	background: #444;
}

.timeline-event-bubble-time {
	display: none;
}

.timeline-event-bubble-image {
	float: none;
}

.timeline-band-events {
	padding-top: 15px;
}

div.timeline-event-icon-default {
	border: none;
	padding: 0px;
}

.timeline-entity-subtext {
	margin-bottom: 3px;
	padding-bottom: 3px;
}

.timeline-tidypics-image-container {
	min-height: 180px;
}

.timeline-message-container div {
	background: #555555 !important;
}

.timeline-message-container {
	border: 3px solid #222222;
}

.timeline-message img {
	display: none;
}

/** Image Popup **/
.image-popup-dialog {
	display: none;
	padding: 10px;
	border: 8px solid #555555;
	background: #ffffff;
	-moz-border-radius:5px 5px 5px 5px;
	-webkit-border-radius: 5px 5px 5px 5px;
}

/** Tidypics Overrides **/
.tagdashboard-module .tidypics_album_images,
.tagdashboard-module .tidypics_album_gallery_item {
	text-align:center;
	width:130px; 
	height:130px;
}

.tagdashboard-module .tidypics_album_images img,
.tagdashboard-module .tidypics_album_gallery_item img {
	width: 130px;
	height: 130px;
}

.tagdashboard-module .elgg-gallery {
	text-align: center;
}

/** Timeline dark theme **/
/*---------------------------------*/

.dark-theme { color: #eee; }
.dark-theme .timeline-band-0 .timeline-ether-bg { background-color: #111 }
.dark-theme .timeline-band-1 .timeline-ether-bg { background-color: #333 }
.dark-theme .timeline-band-2 .timeline-ether-bg { background-color: #222 }
.dark-theme .timeline-band-3 .timeline-ether-bg { background-color: #444 }

.dark-theme .t-highlight1 { background-color: #003; }
.dark-theme .p-highlight1 { background-color: #300; }

.dark-theme .timeline-highlight-label-start .label_t-highlight1 { color: #f00; }
.dark-theme .timeline-highlight-label-end .label_t-highlight1 { color: #115; }

.dark-theme .timeline-band-events .important { color: #c00; }
.dark-theme .timeline-band-events .small-important { background: #c00; }

.dark-theme .timeline-date-label-em { color: #fff; }
.dark-theme .timeline-ether-lines { border-color: #555; border-style: solid; }
.dark-theme .timeline-ether-highlight { background: #555; }

.dark-theme .timeline-event-tape,
.dark-theme .timeline-small-event-tape { background: #f60; }
.dark-theme .timeline-ether-weekends { background: #111; }
