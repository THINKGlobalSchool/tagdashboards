<?php
/**
 * Ubertags JS library
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */ 
?>

elgg.provide('elgg.ubertags');
elgg.provide('elgg.ubertags.timeline');

// Contants
elgg.ubertags.SUBTYPE = 'subtype';
elgg.ubertags.TAG = 'tag';

// Init function
elgg.ubertags.init = function () {
	console.log('Ubertags Loaded');
}

// Loads the ubertags results, this is wrapped by a validator: submit_search
elgg.ubertags.load_ubertags_results = function(search, group_by) {
	var url = elgg.normalize_url('pg/ubertags/searchubertag?group=' + group_by + '&search=' + search);
	$('#ubertags-content-container').hide().load(url, function() {
		$('#ubertags-content-container').fadeIn('fast');
	});
	return false;
}

// Validate and submit and ubertag search
elgg.ubertags.submit_search = function (value, group_by) {
	if (value) {
		// Make sure group_by is valid (either subtype or tag)
		if (!group_by || (group_by != elgg.ubertags.SUBTYPE || group_by != elgg.ubertags.TAG)) {
			group_by = elgg.ubertags.SUBTYPE;
		}
		elgg.ubertags.load_ubertags_results(value, group_by);
		$('a#show_hide').show();
		$('span#ubertags-search-error').html('');
		window.location.hash = encodeURI(value); // Hash magic for permalinks
	} else {
		$('a#show_hide').hide();
		$('span#ubertags-search-error').html('Please enter text to search');
		$('#ubertags-content-container').html('');
	}
	
}

// Get search value
elgg.ubertags.get_ubertag_search_value = function () {
	var value = $('#ubertags-search-input').val();
	value = value.toLowerCase();
	return value;
}

elgg.ubertags.load_ubertags_subtype_content = function (subtype, search, offset) {
	var end_url = elgg.normalize_url('pg/ubertags/loadsubtype/');
	end_url += "?subtype=" + subtype + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + subtype + "_content").load(end_url, '', function() {
		$("#loading_" + subtype).hide();
	});
	
	/** This was tricky.. not deleting this ever. 
	
	$("#loading_" + subtype).show();
	$("#" + subtype + "_content").hide();
	$("#" + subtype + "_content").load(end_url, '', function () {
		$("#loading_" + subtype).fadeOut('fast', function () {
			$("#" + subtype + "_content").fadeIn('fast');
		});
	});
	
	**/
	
	return false;
}

elgg.ubertags.fade_div = function(id) {
	$("#uberview_entity_list_" + id).fadeOut('fast', function () {
		$("#loading_" + id).show();
	});
}

// Timeline functions..

/* Creates a popup out of a dialog with given id */
elgg.ubertags.timeline.timeline_create_popup_with_id = function (id, width) {
	if (!width) {
		width = 'auto';
	}
	
	elgg.ubertags.timeline.dlg = $("#" + id).dialog({
						autoOpen: false,
						width: width, 
						height: 'auto',
						modal: true,
						open: function(event, ui) { 
							console.log('opening');
							$(".ui-dialog-titlebar-close").hide(); 	
						},
						buttons: {
							"X": function() { 
								$(this).dialog("close"); 
							} 
	}});
}

/* Loads a popup with given url */
elgg.ubertags.timeline.timeline_load_popup_by_id = function (id, load_url) {
	elgg.ubertags.timeline.timeline_create_popup_with_id(id, 500);
	$("#" + id).dialog("open");
	$("#" + id).load(load_url);
}

/* Creates an image popup with given src */
elgg.ubertags.timeline.timeline_show_image_popup_by_id = function (id, src) {
	elgg.ubertags.timeline.timeline_create_popup_with_id(id, 640);
	$("#" + id).dialog("open");
	$("#" + id).html("<img src='" + src + "' />");
}

elgg.register_event_handler('init', 'system', elgg.ubertags.init);
