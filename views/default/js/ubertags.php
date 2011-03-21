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

// Init function
elgg.ubertags.init = function () {
	// Nothing to do here at the moment
}

// Validate and submit and ubertag search
elgg.ubertags.submit_search = function (value, type, subtypes) {
	if (value) {
		if (type == 'custom') {
			// Grab the custom tags string
			var tag_string = elgg.ubertags.get_ubertag_custom_value();
			
			// Trim whitespace
			tag_string = $.trim(tag_string);
			
			// Trim any trailing commas, sometimes these get in there
			var len = tag_string.length;
			if (tag_string.substr(len-1,1) == ",") {
				tag_string = tag_string.substring(0,len-1);
			}
			
			// Split into an array
			var tag_array = tag_string.split(',');
			
			// Nead little jquery hack to remove empty array elements
			tag_array = $.grep(tag_array,function(n,i){
			    return(n);
			});
			
			// Handle any weird whitespace issues in supplied tags (trim 'em)
			$.each(tag_array, function(i, v) {
				tag_array[i] = $.trim(v);
			});
		}
		var url = elgg.normalize_url('pg/ubertags/searchubertag?type=' + type + '&search=' + value);
		$('#ubertags-content-container').hide().load(url, { 'custom[]': tag_array, 'subtypes' : subtypes }, function() {
			$('#ubertags-content-container').fadeIn('fast');
		});
		$('a#ubertags-options-toggle').show();
		$('#ubertags-save-input-container').show();
		$('span#ubertags-search-error').html('');
		window.location.hash = encodeURI(value); // Hash magic for permalinks
	} else {
		$('a#ubertags-options-toggle').hide();
		$('#ubertags-save-input-container').hide();
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

// Get search value
elgg.ubertags.get_ubertag_custom_value = function () {
	var value = $('#ubertags-custom-input').val();
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

elgg.ubertags.load_ubertags_activity_content = function (activity, container_guid, offset) {
	var end_url = elgg.normalize_url('pg/ubertags/loadactivity/');
	end_url += "?activity=" + activity + "&container_guid=" + container_guid;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + activity + "_content").load(end_url, '', function() {
		$("#loading_" + activity).hide();
	});	
	return false;
}

elgg.ubertags.load_ubertags_activity_tag_content = function (activity, search, subtypes, offset) {
	var end_url = elgg.normalize_url('pg/ubertags/loadactivitytag/');
	end_url += "?activity=" + activity + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + activity + "_content").load(end_url, { 'subtypes' : subtypes }, function() {
		$("#loading_" + activity).hide();
	});	
	return false;
}

elgg.ubertags.load_ubertags_custom_content = function (group, search, subtypes, offset) {
	var end_url = elgg.normalize_url('pg/ubertags/loadcustom/');
	end_url += "?group=" + group + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + group + "_content").load(end_url, { 'subtypes' : subtypes }, function() {
		$("#loading_" + group).hide();
	});	
	return false;
}

elgg.ubertags.fade_div = function(id) {
	$("#uberview_entity_list_" + id).fadeOut('fast', function () {
		$("#loading_" + id).show();
	});
}

elgg.ubertags.ubertags_switch_groupby = function(tab_id, groupby_val) {
	var nav_name = "input#" + tab_id;
	var tab_name = "div#" + tab_id;
	
	console.log(nav_name);
	console.log(tab_name);

	$(".ubertags-groupby-div").hide();
	$(tab_name).show();
	$(".ubertags-groupby-radio").attr('checked', false);
	
	$(nav_name).attr('checked', true);
	$("#ubertag-groupby").val(groupby_val);
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
