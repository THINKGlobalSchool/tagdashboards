<?php
/**
 * Ubertags custom image object view
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 */
?>
<div class="tidypics_album_images">
	<a href="<?php echo $vars['entity']->getURL();?>"><img src="<?php echo elgg_get_site_url(); ?>pg/photos/thumbnail/<?php echo $vars['entity']->getGUID();?>/small/" alt="<?php echo $vars['entity']->title; ?>"/></a>
</div>
