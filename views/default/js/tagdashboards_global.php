<?php
/**
 * Tag Dashboards Global JS library
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010 - 2014
 * @link http://www.thinkglobalschool.com/
 */ 
?>
//<script>
elgg.provide('elgg.tagdashboards_global');

// Init function
elgg.tagdashboards_global.init = function () {
	elgg.tagdashboards_global.initAddTagLightbox();
}

/**
 * Init lightboxes (can be called manually)
 */
elgg.tagdashboards_global.initAddTagLightbox = function() {
	// Make sure events are only delegated once
	$(document).undelegate('.td-add-tag-submit', 'click');
	$('.add-tag').die();


	// Register click handler for add tag submit
	$(document).delegate('.td-add-tag-submit', 'click', elgg.tagdashboards_global.addTagClick);

	$('.add-tag').colorbox({
		'initialWidth' : '50',
		'initialHeight' : '50',
		'title' : function() {
			return "<h2>" + $(this).attr('title') + "</h2>";
		},
		'onComplete' : function() {
			$(this).colorbox.resize();
		},
		'onOpen' : function() {
			$(this).removeClass('cboxElement');
		},
		'onClosed' : function() {
			$(this).addClass('cboxElement');
		}
	});	
}

// Click handler for move to group submit button
elgg.tagdashboards_global.addTagClick = function(event) {	
	var $_this = $(this);
	
	$_this.attr('disabled', 'DISABLED');

	var $form = $(this).closest('form');
	var values = {};
	$.each($form.serializeArray(), function(i, field) {
	    values[field.name] = field.value;
	});

	if (!values.tag) {
		elgg.register_error(elgg.echo('tagdashboards:error:notag'));
		$_this.removeAttr('disabled', 'DISABLED');
	} else {
		$.colorbox.close();
		elgg.portfolio.tagEntity($(this), values.entity_guid, values.tag);
	}

	event.preventDefault();
}

elgg.register_hook_handler('init', 'system', elgg.tagdashboards_global.init);
elgg.register_hook_handler('content_loaded', 'filtrate', elgg.tagdashboards_global.initAddTagLightbox);
elgg.register_hook_handler('photoLightboxAfterShow', 'tidypics', elgg.tagdashboards_global.initAddTagLightbox);