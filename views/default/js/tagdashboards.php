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
//<script>
elgg.provide('elgg.tagdashboards');

// Init function
elgg.tagdashboards.init = function () {
	
	// Init each element matching the autocomplete class
	$('input.tagdashboards-autocomplete-tags').each(function() {
		$(this).autocomplete({
			source: function(request, response) {
				var params = {term: $(this).val()};
				elgg.get('tagdashboards/tags', {
					data: params,
					dataType: 'json',
					success: function(data) {
						response(data);
					},
				});
			},
			appendTo: $(this).parent(),
			minLength: 2,
			select: function() {},
		});
	});

}

/**
 * Display tag dashboard based on given values
 * @param array options:
 * 
 * search => NULL|STRING search for tag (can be empty for 'all')
 * 
 * type => STRING type of search (custom, subtype or activity)
 * 
 * subtypes => Array of subtypes to include
 * 
 * owner_guids => NULL|Array of owner_guids to include 
 * 
 * custom_tags => NULL|Array custom tags to group content
 */
elgg.tagdashboards.display = function (options) {
	// Get options
	var type 		= options['type'];
	var search 		= options['search'];
	var subtypes 	= options['subtypes'];
	var owner_guids	= options['owner_guids'];
	var custom_tags	= elgg.tagdashboards.custom_tags_string_to_array(options['custom_tags']);
	var lower_date 	= options['lower_date'];
	var upper_date 	= options['upper_date'];

	// Create url to load
	var url = elgg.normalize_url('tagdashboards/loadtagdashboard?type=' + type);
	
	if (search) {
		url += '&search=' + search;
	}
	
	// Load in content
	$('#tagdashboards-content-container').hide().load(url, { 
		'custom_tags[]': 	custom_tags, 
		'subtypes': 		subtypes , 
		'owner_guids': 		owner_guids,
		'lower_date': 		lower_date,
		'upper_date': 		upper_date, 
		}, 
		function() {$('#tagdashboards-content-container').fadeIn('fast');}
	);
	$('#tagdashboards-save-input-container').show();
}

/**
 * Display an error in the specified element (id) with given txt
 */
elgg.tagdashboards.display_error = function(id, txt) {
		$('a#tagdashboards-options-toggle').hide();
		$('#tagdashboards-save-input-container').hide();
		$('#' + id).html(txt);
		$('#tagdashboards-content-container').html('');
}

// Get search value
elgg.tagdashboards.get_tagdashboard_search_value = function () {
	var value = $('#tagdashboards-search-input').val();
	value = value.toLowerCase();
	return value;
}

// Validate, and format custom tag string as an array
elgg.tagdashboards.custom_tags_string_to_array = function (tag_string) {
	// If string is empty, return empty array
	if (!tag_string) {
		return new Array();
	} else if (tag_string instanceof Array) { // If we were supplied with an array, just return it
		return tag_string;
	}
	// To lower case
	tag_string = tag_string.toLowerCase();
	
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
	
	//var value = $('#tagdashboards-custom-input').val();
	
	return tag_array;
}

elgg.tagdashboards.load_tagdashboards_subtype_content = function (subtype, search, owner_guids, lower_date, upper_date, offset) {
	var end_url = elgg.normalize_url('tagdashboards/loadsubtype/');
	end_url += "?subtype=" + subtype + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + subtype + "_content").load(end_url, {'owner_guids' : owner_guids, 'lower_date' : lower_date, 'upper_date' : upper_date}, function() {
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
	var end_url = elgg.normalize_url('tagdashboards/loadactivity/');
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

elgg.tagdashboards.load_tagdashboards_activity_tag_content = function (activity, search, subtypes, owner_guids, lower_date, upper_date, offset) {
	var end_url = elgg.normalize_url('tagdashboards/loadactivitytag/');
	end_url += "?activity=" + activity + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + activity + "_content").load(end_url, { 'subtypes' : subtypes, 'owner_guids' : owner_guids, 'lower_date' : lower_date, 'upper_date' : upper_date}, function() {
		$("#loading_" + activity).hide();
	});	
	return false;
}

elgg.tagdashboards.load_tagdashboards_custom_content = function (group, search, subtypes, owner_guids, lower_date, upper_date, offset) {
	var end_url = elgg.normalize_url('tagdashboards/loadcustom/');
	end_url += "?group=" + group + "&search=" + search;
	if (offset) {
		end_url += "&offset=" + offset;
	}

	/* Simple show/hide */
	$("#" + group + "_content").load(end_url, { 'subtypes' : subtypes, 'owner_guids' : owner_guids, 'lower_date' : lower_date, 'upper_date' : upper_date }, function() {
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

	$(".tagdashboards-groupby-div").hide();
	$(tab_name).show();
	$(".tagdashboards-groupby-radio").attr('checked', false);
	
	$(nav_name).attr('checked', true);
	$("#tagdashboard-groupby").val(groupby_val);
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards.init);
//</script>