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

// Flag for group selected
elgg.tagdashboards.group_selected = false;

// Init function
elgg.tagdashboards.init = function () {
	// Setup and initialize tag autocomplete inputs
	elgg.tagdashboards.init_autocomplete_inputs();

	// Setup and initialize tag dashboards
	elgg.tagdashboards.init_dashboards();

	// Setup the save form
	elgg.tagdashboards.init_save_form();

	// ** Misc features **
	// Arrow toggler
	$('.tagdashboards-arrow-toggler').each(elgg.tagdashboards.init_togglers);
	
	$('.tagdashboards-check-column').live('click', elgg.tagdashboards.toggle_column);

	// Group select
	$('.tagdashboards-group-select-link').fancybox({
		'onClosed' : function() {
			// Uncheck the group select toggle if no group was selected
			if (!elgg.tagdashboards.group_selected) {
				$('.tagdashboards-toggle-group-select').attr('checked', false);
			}
		}
	});

	// Toggle group select
	$('.tagdashboards-toggle-group-select').live('click', elgg.tagdashboards.toggle_group_select);
	
	// Toggle 'this' group select
	$('.tagdashboards-toggle-this-group-select').live('click', elgg.tagdashboards.toggle_this_group_select);

	// Fix userpickers.. 
	elgg.tagdashboards.fixUserPickers();

	// Make group pagination load in the container 
	$(document).delegate('#tagdashboards-group-select-container .elgg-pagination a','click', function(event) {
		$container = $(this).closest('#tagdashboards-group-select-list');

		var height = $container.height();

		$container.html("<div style='height: " + height + "px' class='elgg-ajax-loader'></div>").css({
			'height': height,
		}).load($(this).attr('href'), function() {
			$(this).css({'height':'auto'});
		});

		event.stopPropagation(); // Don't propagate the click event.. this messes with popups, etc
		event.preventDefault();
	});

	$(document).delegate('#tagdashboards-group-select-submit', 'click', elgg.tagdashboards.group_select_click);

	/** End group select **/

	// Check hashes
	elgg.tagdashboards.handle_hash();

	// Handle click for timeline/media/content buttons
	$('.switch-tagdashboards').live('click', function(event) {		
		var params = {
			href: $(this).attr('href'),
		}

		$(".tagdashboard-container").hide();
		$(".tagdashboard-media-container").hide();

		if (params.href === '#content') {
			$(".tagdashboard-container").show();
			window.location.hash = '';
		} 

		if (params.href === "#media") {
			$(".tagdashboard-media-container").show();
			window.location.hash = "media";

			$('#tagdashboards-media-videos-coverflow').html('');
			if (typeof init_coverflow == 'function') {
				init_coverflow(playlist, mid);
			}
		}

		elgg.trigger_hook('toggle_view', 'tagdashboards', params);
		event.preventDefault();
	});

	// Register modules populated hook for simplekaltura videos
	elgg.register_hook_handler('populated', 'modules', elgg.simplekaltura_utility.lightbox_init);
}

/**	
 * Convenience function to initialize autocompletes
 */
elgg.tagdashboards.init_autocomplete_inputs = function() {
	// Init each element matching the autocomplete class
	$('input.tagdashboards-autocomplete-tags').each(function() {
		var input = $(this);
		$(this).autocomplete({
			source: function(request, response) {
				var params = {term: input.val()};
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
 * Initialized tag dashboards data to be displayed
 */
elgg.tagdashboards.init_dashboards = function() {
	$('div.tagdashboard-container').each(function() {
		elgg.tagdashboards.init_dashboards_with_container($(this));
	});
}

/**
 * Initialize specific tagdashboard
 */
elgg.tagdashboards.init_dashboards_with_container = function(container) {
	var options = {};
	container.find('div.tagdashboard-options').find('input').each(function() {
		var value = $(this).val();
		// Try to parse JSON
		try {
			value = $.parseJSON(value);
		} catch (e) {
			// Do nothing
		} finally {
			options[$(this).attr('name')] = value;
		}
	});
	// Display it
	elgg.tagdashboards.display(options);
}

/**	
 * Helper function to setup events and init the save form
 */
elgg.tagdashboards.init_save_form = function() {
	
	// Groupby radio buttons show/hide
	$('.tagdashboards-groupby-radio li label input').click(elgg.tagdashboards.groupby_switcher);
	$('#tagdashboards-groupby-div-' + $('#tagdashboard-groupby-input:checked').val()).show();
	
	// Hide container by default
	$('#tagdashboards-save-container').hide();
	
	
	// Submit handler for search submit
	$('#tagdashboards-search-submit').live('click', function(event){
		elgg.tagdashboards.validate_search();
		event.preventDefault();
	});

	// Process form on 'enter'
	// $('#tagdashboards-search-input').keypress(function(event){
	// 	if (event.which == 13) {
	// 		elgg.tagdashboards.validate_search();
	// 		event.preventDefault();
	// 	}
	// });
	
	// Refresh handler
	$('#tagdashboards-refresh-input').live('click', elgg.tagdashboards.display_from_form);
}

/**
 * Helper function to init arrow togglers
 */
elgg.tagdashboards.init_togglers = function() {
	$(this).html(elgg.echo($(this).attr('name')) + ' &#9660;');
	$(this).click(elgg.tagdashboards.tdtoggler);
}

/**
 * Helper function to grab form values and display a tag dashboard
 */
elgg.tagdashboards.display_from_form = function(event) {
	// Grab selected subtypes
	var inputs = $('.tagdashboards-subtype-input:checked');
	var selected_subtypes = {};
	count = 0;
	inputs.each(function() {
		selected_subtypes[count] = $(this).val();
		count++;
	});

	// Get search value
	search = elgg.tagdashboards.get_search();	

	// Get owner guids (check both inputs called members, for userpicker and any called owner_guids)
	var owner_guids_input = $('#tagdashboards-filter-input input[name="members[]"], #tagdashboards-filter-input input[name="owner_guids[]"]');

	var owner_guids = [];

	count = 0;
	owner_guids_input.each(function() {
		owner_guids[count] = $(this).val();
		count++;
	});

	// Get user guids for users group option
	var user_guids_input =  $('#tagdashboards-groupby-div-users input[name="members[]"]');

	var user_guids = [];

	count = 0;
	user_guids_input.each(function() {
		user_guids[count] = $(this).val();
		count++;
	});

	// Set up options
	var options = new Array();
	options['search'] = search;
	options['type'] = $('#tagdashboard-groupby-input:checked').val();
	options['subtypes'] = selected_subtypes;
	options['custom_tags'] = $('input[name=custom]').val();
	options['owner_guids'] = owner_guids;
	options['user_guids'] = user_guids;

	// Check for group content option
	var $group_content_checked = $('#tagdashboards-filter-input input:checkbox[name="group_content"]');
	if ($group_content_checked.is(':checked')) {
		options['container_guid'] = $('input:hidden[name="container_guid"]').val();
	}
	// Get/parse dates
	options['lower_date'] = Date.parse($('#tagdashboard-date-range-from').val()) / 1000;
	options['upper_date'] = Date.parse($('#tagdashboard-date-range-to').val()) / 1000;

	elgg.tagdashboards.display(options);
	
	if (event) {
		event.preventDefault();
	}
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
	var type 		   = options['type'];
	var search 		   = options['search'];
	var subtypes 	   = options['subtypes'];
	var owner_guids	   = options['owner_guids'];
	var user_guids	   = options['user_guids'];
	var container_guid = options['container_guid'] ? options['container_guid'] : '';
	var custom_tags	   = elgg.tagdashboards.custom_tags_string_to_array(options['custom_tags']);
	var custom_titles  = elgg.tagdashboards.custom_tags_string_to_array(options['custom_titles']);
	var lower_date 	   = options['lower_date'] ? options['lower_date'] : '';
	var upper_date 	   = options['upper_date'] ? options['upper_date'] : '';

	// Create url to load
	var url = elgg.normalize_url('tagdashboards/loadtagdashboard?type=' + type);
	
	if (search) {
		url += '&search=' + search;
	}

	// Make sure owner guids is an array (if set)
	if (owner_guids && !$.isArray(owner_guids)) {
		owner_guids = [owner_guids];
	}

	// Make sure user guids is an array (if set)
	if (user_guids && !$.isArray(user_guids)) {
		user_guids = [user_guids];
	}
	
	// Load in content
	$('.tagdashboards-content-container').hide().load(url, { 
		'custom_tags[]': 	custom_tags, 
		'custom_titles[]':  custom_titles,
		'subtypes': 		subtypes , 
		'owner_guids': 		owner_guids,
		'user_guids': 		user_guids,
		'container_guid':   container_guid,
		'lower_date': 		lower_date,
		'upper_date': 		upper_date, 
		}, 
		function() {
			$('.tagdashboards-content-container').fadeIn('fast');
		}
	);
	$('#tagdashboards-save-input-container').show();
}

/**
 * Helper function to handle a window hash
 */
elgg.tagdashboards.handle_hash = function() {
	// If we have a hash up in the address, search automatically
	if (window.location.hash && window.location.hash != '#comments' && window.location.hash != "#timeline" && window.location.hash != '#media') {
		$('a#tagdashboards-options-toggle').show();
		$('#tagdashboards-save-input-container').show();
		
		var hash = decodeURI(window.location.hash.substring(1));
		var input = $('#tagdashboards-search-container input[name=search]');
		tags = hash.split(',');
		$.each(tags, function(idx, value){
			elgg.typeaheadtags.addTag(value, input);
		});

		elgg.tagdashboards.set_dashboard_title();
		
		// Display from form
		elgg.tagdashboards.display_from_form(false);
	} 

	if (window.location.hash === '#media') {
		$(".tagdashboard-container").hide();
		$(".tagdashboard-media-container").show();

		if (typeof init_coverflow == 'function') {
			init_coverflow(playlist, mid);
		}
	} 

	if (window.location.hash === '#timeline') {
		$(".tagdashboard-media-container").hide();
		$(".tagdashboard-container").hide();
	}
	return;
}

/**
 * Validate and submit search, also handles hiding/showing UI elements
 */
elgg.tagdashboards.validate_search = function() {
	if (!$('#tagdashboards-search-container input[name=search]').val()) {
		elgg.register_error(elgg.echo('tagdashboards:error:nosearch'));
	} else {
		$('a#tagdashboards-options-toggle').show();
		$('span#tagdashboards-search-error').html('');
		
		// Set title
		elgg.tagdashboards.set_dashboard_title();
		
		// Display from form
		elgg.tagdashboards.display_from_form(false);

		if (window.location.href.indexOf("tagdashboards/edit") === -1) {
			window.location.hash = elgg.tagdashboards.get_search(); // Hash magic for permalinks
		}
	}
}

/**
 * Set dashboard title input
 */
elgg.tagdashboards.set_dashboard_title = function() {
	$('#tagdashboard-title').val(decodeURI(elgg.tagdashboards.get_search()));
}

/**
 * Get and format a forms search value
 */
elgg.tagdashboards.get_search = function () {
	var value = $('#tagdashboards-search-container input[name=search]').val();
	if (value) {
		value = value.toLowerCase();
		value = value.substring(0, value.lastIndexOf(','));
		value = encodeURI(value);
		return value;
	}
	return false;
}


/**
 * Special Arrow Toggler
 * uses: 
 * 	href - id of div to toggle
 * 	name - label for link
 */
elgg.tagdashboards.tdtoggler = function(event) {
	var id = $(this).attr('href');
	var label = $(this).attr('name');
	
	if ($(id).is(':visible')) {
		$(this).html(elgg.echo(label) + " &#9660;");
	} else {
		$(this).html(elgg.echo(label) + " &#9650;");
	}
	$(id).toggle('slow');
	event.preventDefault();
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
	
	return tag_array;
}

/** 
 * Toggle columns
 */
elgg.tagdashboards.toggle_column = function(event) {
	$('.tagdashboards-content-container').each(function() {
		if ($(this).hasClass('no-float')) {
			$(this).removeClass('no-float');
		} else {
			$(this).addClass('no-float');
		}
	});
}

/**
 * Toggle group users select
 */
elgg.tagdashboards.toggle_group_select = function(event) {
	if ($(this).is(':checked')) {
		elgg.tagdashboards.group_selected = false;
		$('.tagdashboards-group-select-link').trigger('click');
	} else {
		// Clear userpicker data
		elgg.userpicker.userList = [];
		$('.tagdashboards-groupby-description .elgg-user-picker-list').children().remove();
	}
}

/**
 * Toggle this group users select
 */
elgg.tagdashboards.toggle_this_group_select = function(event) {
	if ($(this).is(':checked')) {
		var guid = $(this).attr('id');

		var _this = $(this);
		
		// Get group members
		elgg.action('tagdashboards/group_members', {
			data: {
				group_guid: guid
			},
			success: function(response) {
				if (response.status == 0) {
					// Clear any user pickers
					$('ul.elgg-user-picker-list').children().remove();

					// Rebuild the user list
					elgg.userpicker.userList = {};
					var picker_list = _this.parent().find('div.elgg-user-picker').find('ul.elgg-user-picker-list');
					picker_list.children().each(function() {
						elgg.userpicker.userList[$(this).find('input').val()] = true;
					});

					elgg.tagdashboards.group_selected = true;
					for (idx in response.output) {
						elgg.tagdashboards.addUserToPicker(response.output[idx]);
					}
				} else {
					// Error..
				}
				$.fancybox.close();
			}
		});
	} else {
		// Clear userpicker data
		elgg.userpicker.userList = [];
		$('.tagdashboards-groupby-description .elgg-user-picker-list').children().remove();
	}
}

/**
 * Group select click handler
 */
elgg.tagdashboards.group_select_click = function(event) {
	var value = $('input[name="select_group_guid"]:checked').val();

	var checkbox = $('.tagdashboards-toggle-group-select');

	if (!value) {
		elgg.register_error(elgg.echo('tagdashboards:error:selectgroup'));
	} else {
		// Get group members
		elgg.action('tagdashboards/group_members', {
			data: {
				group_guid: value
			},
			success: function(response) {
				if (response.status == 0) {
					// Clear any user pickers
					$('ul.elgg-user-picker-list').children().remove();

					// Rebuild the user list
					elgg.userpicker.userList = {};
					var picker_list = checkbox.parent().find('div.elgg-user-picker').find('ul.elgg-user-picker-list');
					picker_list.children().each(function() {
						elgg.userpicker.userList[$(this).find('input').val()] = true;
					});

					elgg.tagdashboards.group_selected = true;
					for (idx in response.output) {
						elgg.tagdashboards.addUserToPicker(response.output[idx]);
					}
				} else {
					// Error..
				}
				$.fancybox.close();
			}
		});
	}
	event.preventDefault();
}

/**
 * Helper function to reproduce the userpicker addUser function
 */
elgg.tagdashboards.addUserToPicker = function(user_info) {
	if (!(user_info.guid in elgg.userpicker.userList)) {
		elgg.userpicker.userList[user_info.guid] = true;
		var users = $('.tagdashboards-groupby-description .elgg-user-picker-list');
		var li = '<input type="hidden" name="members[]" value="' + user_info.guid + '" />';
		li += elgg.userpicker.viewUser(user_info);
		$('<li>').html(li).appendTo(users);
	}
}

/** 
 * Simple switcher function for the groupby inputs
 */
elgg.tagdashboards.groupby_switcher = function(event) {
	$('.tagdashboards-groupby-div').hide();
	$('#tagdashboards-groupby-div-' + $(this).val()).show();
}

// Temporary fix for multiple user pickers on tag dashboard edit form 
elgg.tagdashboards.fixUserPickers = function(event) {
	var pickers = $('#tagdashboards-save-container').find('.elgg-input-user-picker').bind('mousedown', function(event){
		var _this = $(this);
		$('#tagdashboards-save-container').find('.elgg-input-user-picker').each(function(idx, value) {
			// Clear the OTHER userpicker
			if (!$(this).is(_this)) {
				$(this).parent().find('ul.elgg-user-picker-list').children().remove();
			} else {
				// Rebuild the user list
				elgg.userpicker.userList = {};
				$(this).parent().find('ul.elgg-user-picker-list').children().each(function() {
					elgg.userpicker.userList[$(this).find('input').val()] = true;
				});
			}
		});
	});
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards.init);