<?php
/**
 * Tag Dashboards JS library
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */ 
?>

elgg.provide('elgg.tagdashboards');
elgg.provide('elgg.tagdashboards.timeline');

// Init function
elgg.tagdashboards.init = function () {
	// Nothing to do here at the moment
}

// Validate and submit and tagdashboard search
elgg.tagdashboards.submit_search = function (value, type, subtypes, any_value) {
	// Search any value?
	if (!any_value) {
		any_value = false;
	}

	// If we're supplied a search value or we're not looking for a value
	if (value || any_value) {
		// If custom, validate and clean custom values
		if (type == 'custom') {
			// Grab the custom tags string
			var tag_string = elgg.tagdashboards.get_tagdashboard_custom_value();

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
				
		var url = elgg.normalize_url('pg/tagdashboards/searchtagdashboard?type=' + type + '&search=' + value);
		$('#tagdashboards-content-container').hide().load(url, { 'custom[]': tag_array, 'subtypes' : subtypes }, function() {
			$('#tagdashboards-content-container').fadeIn('fast');
		});
		$('a#tagdashboards-options-toggle').show();
		$('#tagdashboards-save-input-container').show();
		$('span#tagdashboards-search-error').html('');
		window.location.hash = encodeURI(value); // Hash magic for permalinks
	} else { // No value supplied, show error
		$('a#tagdashboards-options-toggle').hide();
		$('#tagdashboards-save-input-container').hide();
		$('span#tagdashboards-search-error').html('Please enter text to search');
		$('#tagdashboards-content-container').html('');
	}
	
}

// Get search value
elgg.tagdashboards.get_tagdashboard_search_value = function () {
	var value = $('#tagdashboards-search-input').val();
	value = value.toLowerCase();
	return value;
}

// Get search value
elgg.tagdashboards.get_tagdashboard_custom_value = function () {
	var value = $('#tagdashboards-custom-input').val();
	value = value.toLowerCase();
	return value;
}

elgg.tagdashboards.load_tagdashboards_subtype_content = function (subtype, search, offset) {
	var end_url = elgg.normalize_url('pg/tagdashboards/loadsubtype/');
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

elgg.tagdashboards.load_tagdashboards_activity_content = function (activity, container_guid, offset) {
	var end_url = elgg.normalize_url('pg/tagdashboards/loadactivity/');
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

elgg.tagdashboards.load_tagdashboards_activity_tag_content = function (activity, search, subtypes, offset) {
	var end_url = elgg.normalize_url('pg/tagdashboards/loadactivitytag/');
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

elgg.tagdashboards.load_tagdashboards_custom_content = function (group, search, subtypes, offset) {
	var end_url = elgg.normalize_url('pg/tagdashboards/loadcustom/');
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

elgg.tagdashboards.fade_div = function(id) {
	$("#uberview_entity_list_" + id).fadeOut('fast', function () {
		$("#loading_" + id).show();
	});
}

elgg.tagdashboards.tagdashboards_switch_groupby = function(tab_id, groupby_val) {
	var nav_name = "input#" + tab_id;
	var tab_name = "div#" + tab_id;
	
	console.log(nav_name);
	console.log(tab_name);

	$(".tagdashboards-groupby-div").hide();
	$(tab_name).show();
	$(".tagdashboards-groupby-radio").attr('checked', false);
	
	$(nav_name).attr('checked', true);
	$("#tagdashboard-groupby").val(groupby_val);
}

// Timeline functions..

/* Creates a popup out of a dialog with given id */
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

/* Loads a popup with given url */
elgg.tagdashboards.timeline.timeline_load_popup_by_id = function (id, load_url) {
	elgg.tagdashboards.timeline.timeline_create_popup_with_id(id, 500);
	$("#" + id).dialog("open");
	$("#" + id).load(load_url);
}

/* Creates an image popup with given src */
elgg.tagdashboards.timeline.timeline_show_image_popup_by_id = function (id, src) {
	elgg.tagdashboards.timeline.timeline_create_popup_with_id(id, 640);
	$("#" + id).dialog("open");
	$("#" + id).html("<img src='" + src + "' />");
}

elgg.register_event_handler('init', 'system', elgg.tagdashboards.init);
