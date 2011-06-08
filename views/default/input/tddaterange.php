<?php
/**
 * tagdashboard date range input
 * 
 * @package Tag Dashboards
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 * @uses $vars['name'] 	Name of the inputs (will have _to or _from appended)
 * @uses $vars['id']	Input ID (will have -to or -from appended)
 * @uses $vars['value_lower'] 	Lower initial value (optional)
 * @uses $vars['value_upper'] 	Upper initial value (optional)
 */

$name 	= $vars['name'];
$id 	= $vars['id'];
$lower 	= $vars['value_lower'] ? date("F j, Y",$vars['value_lower']) : '';
$upper 	= $vars['value_upper'] ? date("F j, Y",$vars['value_upper']) : '';

echo <<<HTML
	<div id='daterange-container' style='position:relative;'>
		<label>From</label>
		<input class='tagdashboards-daterange' type="text" id="{$id}-from" name="lower_date" value="{$lower}" />
		<label>to</label>
		<input class='tagdashboards-daterange' type="text" id="{$id}-to" name="upper_date" value="{$upper}" />
		<script type='text/javascript'>
			$(function() {
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
			});
		</script>
	</div>
HTML;

return;


$name = $vars['name'];
$id = $vars['id'];
$cleartext = elgg_echo('tagdashboards:label:clear');

echo <<<HTML
	<script type='text/javascript'>
		$(function() {
				$('a.clear-daterange').click(function() {
					$('input.tagdashboards-daterange').val('');
					return false;
				});
			
				var dates = $( "#{$id}-from, #{$id}-to" ).datepicker({
					defaultDate: "+1w",
					changeMonth: true,
					numberOfMonths: 3,
					onSelect: function( selectedDate ) {
						var option = this.id == "{$id}-from" ? "minDate" : "maxDate",
							instance = $( this ).data( "datepicker" ),
							date = $.datepicker.parseDate(
								instance.settings.dateFormat ||
								$.datepicker._defaults.dateFormat,
								selectedDate, instance.settings );
						dates.not( this ).datepicker( "option", option, date );
					}
				});
			});
	</script>
	<label>From</label>
	<input class='tagdashboards-daterange' type="text" id="{$id}-from" name="{$name}_from"/>
	<label>to</label>
	<input class='tagdashboards-daterange' type="text" id="{$id}-to" name="{$name}_to"/>
	<a class='clear-daterange' href='#'>$cleartext</a>
HTML;
