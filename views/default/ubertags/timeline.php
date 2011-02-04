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


// Register JS 
// HAVE TO HAVE TO HAVE TO HAVE TO LOAD THE JS IN THE HEAD!!!
elgg_register_js("http://static.simile.mit.edu/timeline/api-2.3.0/timeline-api.js?bundle=false", 'timeline');
elgg_register_js(elgg_get_site_url() . 'mod/ubertags/lib/ubertags-timeline.js', 'ubertags-timeline');

?>
<script>set_timeline_data_url("<?php echo $json_data_url;?>")</script>
<div id="ubertag-timeline" class='dark-theme'></div>
