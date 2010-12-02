<?php
/**
 * Ubertags generic container
 * - Displays entities with their default listing view
 * 
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.com/
 * 
 */

$params = array(
	'type' => 'object',
	'subtype' => $vars['subtype'],
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 10,
	'full_view' => FALSE,
	'view_type_toggle' => FALSE,
	'pagination' => TRUE,
);

$entity_list = elgg_list_entities($params);

?>
<div class='ubertags_subtype_container'>
<h3 class='ubertags_subtype_title'><?php echo elgg_echo('item:object:' . $vars['subtype']); ?></h3>
<?php echo $entity_list; ?>
</div>