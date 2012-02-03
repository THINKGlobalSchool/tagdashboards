<?php
/**
 * Tag Dashboards Date Range Picker JS
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */ 
?>
//<script>
elgg.provide('elgg.tddaterange');

// Init Daterange picker
elgg.tddaterange.init = function() {
	$('.tagdashboards-daterange').daterangepicker({
		appendTo: 		"div#daterange-container", 
		posX: 			"0",
		posY: 			"0",
		earliestDate: 	Date.parse('-99years'),
		latestDate: 	Date.parse('+99years'),
		dateFormat: 	'MM d, yy',
		onOpen: 		function() {
			//$('input.tagdashboards-daterange').val('');
		},
		defaultDate: 	Date.today(),
	}); 
}

elgg.register_hook_handler('init', 'system', elgg.tddaterange.init);