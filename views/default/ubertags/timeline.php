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

?>
<script>set_timeline_data_url("<?php echo $json_data_url;?>")</script>
<div id="ubertag-timeline" class='dark-theme'></div>
