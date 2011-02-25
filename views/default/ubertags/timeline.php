<?php
/**
 * Ubertags timeline view
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$json_data_url = elgg_get_site_url() . "pg/ubertags/timeline_feed/{$vars['entity']->getGUID()}";


$entity = ubertags_get_last_content($vars['entity']->getGUID());
if ($entity) {
	$latest_date = date('r', strtotime(strftime("%a %b %d %Y", $entity->time_created))); 
}


?>
<script>
	setTimelineDataURL("<?php echo $json_data_url;?>");
	//setLatestDate("<?php echo $latest_date;?>");
</script>
<div style='display: none;' id="info"></div>
<div id="ubertag-timeline-wrapper">
	<div id="ubertag-timeline" class='dark-theme'></div>
</div>