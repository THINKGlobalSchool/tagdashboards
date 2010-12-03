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

/* 
	Setting up a pile of default params. metadata_name_value_pairs
	is what makes the tag magic happen. This might even work 
	for multiple tags. (2 Minutes later..) No it doesn't but 
	that'd be cool.
*/
$params = array(
	'type' => 'object',
	'subtype' => $vars['subtype'],
	'owner' => ELGG_ENTITIES_ANY_VALUE,
	'limit' => 10,
	'full_view' => FALSE,
	'view_type_toggle' => FALSE,
	'pagination' => TRUE,
	'metadata_name_value_pairs' => array(	'name' => 'tags', 
											'value' => $vars['search'], 
											'operands' => 'contains')
);

// See if anyone has registered a hook to display their subtype appropriately
if (!$entity_list = trigger_plugin_hook('ubertags:subtype', $vars['subtype'], array('search' => $vars['search'], 'params' => $params), false)) {
	// Fine, be that way, I'll just dump out this grossness.
	$entity_list = elgg_list_entities($params, 'elgg_get_entities_from_metadata');
} 

?>
<div class='ubertags_subtype_container'>
<h3 class='ubertags_subtype_title'><?php echo elgg_echo('item:object:' . $vars['subtype']); ?></h3>
<?php echo $entity_list; ?>
</div>