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
	
	// Check hashes
	elgg.tagdashboards.handle_hash();
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
		var options = {};
		$(this).find('div.tagdashboard-options').find('input').each(function() {
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
	});
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
	$('#tagdashboards-search-input').keypress(function(event){
		if (event.which == 13) {
			elgg.tagdashboards.validate_search();
			event.preventDefault();
		}
	});
	
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
	var userpicker_input = $('input[name="members[]"], input[name="owner_guids[]"]');

	var owner_guids = [];

	count = 0;
	userpicker_input.each(function() {
		owner_guids[count] = $(this).val();
		count++;
	});

	// Set up options
	var options = new Array();
	options['search'] = search;
	options['type'] = $('#tagdashboard-groupby-input:checked').val();
	options['subtypes'] = selected_subtypes;
	options['custom_tags'] = $('#tagdashboards-custom-input').val();
	options['owner_guids'] = owner_guids;
	
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
	var type 		= options['type'];
	var search 		= options['search'];
	var subtypes 	= options['subtypes'];
	var owner_guids	= options['owner_guids'];
	var custom_tags	= elgg.tagdashboards.custom_tags_string_to_array(options['custom_tags']);
	var lower_date 	= options['lower_date'] ? options['lower_date'] : '';
	var upper_date 	= options['upper_date'] ? options['upper_date'] : '';
	
	// Create url to load
	var url = elgg.normalize_url('tagdashboards/loadtagdashboard?type=' + type);
	
	if (search) {
		url += '&search=' + search;
	}

	// Make sure owner guids is an array (if set)
	if (owner_guids && !$.isArray(owner_guids)) {
		owner_guids = [owner_guids];
	}
	
	// Load in content
	$('.tagdashboards-content-container').hide().load(url, { 
		'custom_tags[]': 	custom_tags, 
		'subtypes': 		subtypes , 
		'owner_guids': 		owner_guids,
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
	if (window.location.hash && window.location.hash != '#comments' && window.location.hash != "#timeline") {
		$('a#tagdashboards-options-toggle').show();
		$('#tagdashboards-save-input-container').show();
		
		var hash = decodeURI(window.location.hash.substring(1));
		var value = $('#tagdashboards-search-input').val(hash);
		elgg.tagdashboards.set_dashboard_title();
		
		// Display from form
		elgg.tagdashboards.display_from_form(false);
	} 
	return;
}

/**
 * Validate and submit search, also handles hiding/showing UI elements
 */
elgg.tagdashboards.validate_search = function() {
	if (!$('#tagdashboards-search-input').val()) {
		elgg.tagdashboards.display_error('tagdashboards-search-error', 'Please enter search text');
	} else {
		$('a#tagdashboards-options-toggle').show();
		$('span#tagdashboards-search-error').html('');
		
		// Set title
		elgg.tagdashboards.set_dashboard_title();
		
		// Display from form
		elgg.tagdashboards.display_from_form(false);
		
		window.location.hash = encodeURI(elgg.tagdashboards.get_search()); // Hash magic for permalinks
	}
}

/**
 * Set dashboard title input
 */
elgg.tagdashboards.set_dashboard_title = function() {
	$('#tagdashboard-title').val(elgg.tagdashboards.get_search);
}

/**
 * Get and format a forms search value
 */
elgg.tagdashboards.get_search = function () {
	var value = $('#tagdashboards-search-input').val();
	if (value) {
		value = value.toLowerCase();
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


/**
 * Display an error in the specified element (id) with given txt
 */
elgg.tagdashboards.display_error = function(id, txt) {
		$('a#tagdashboards-options-toggle').hide();
		$('#tagdashboards-save-input-container').hide();
		$('#' + id).html(txt);
		$('.tagdashboards-content-container').html('');
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
elgg.tagdashboards.toggle_column = function (event) {
	$('.tagdashboards-content-container').each(function() {
		if ($(this).hasClass('no-float')) {
			$(this).removeClass('no-float');
		} else {
			$(this).addClass('no-float');
		}
	});
}


/** 
 * Simple switcher function for the groupby inputs
 */
elgg.tagdashboards.groupby_switcher = function(event) {
	$('.tagdashboards-groupby-div').hide();
	$('#tagdashboards-groupby-div-' + $(this).val()).show();
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards.init);