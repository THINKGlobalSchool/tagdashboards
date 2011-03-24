<?php
/**
 * View a list of entities
 *
 * @package Elgg
 *
 */

$context = $vars['context'];
$offset = $vars['offset'];
$entities = $vars['entities'];
$limit = $vars['limit'];
$count = $vars['count'];
$baseurl = $vars['baseurl'];
$context = $vars['context'];
$listtype = $vars['listtype'];
$pagination = $vars['pagination'];
$fullview = $vars['fullview'];
$uid = uniqid();

if (!$count) {
	return NULL;
}

$spinner = elgg_view('tagdashboards/ajax_spinner', array(
	'id' => 'loading_' . $uid,
	'class' => 'hidden',
));

$html = $spinner . "<div id='uberview_entity_list_$uid' class='hidden'>";
$nav = "";

if (isset($vars['viewtypetoggle'])) {
	$listtypetoggle = $vars['viewtypetoggle'];
} else {
	$listtypetoggle = true;
}

if ($context == "search" && $count > 0 && $listtypetoggle) {
	$nav .= elgg_view('navigation/listtype', array(
		'baseurl' => $baseurl,
		'offset' => $offset,
		'count' => $count,
		'listtype' => $listtype,
	));
}

if ($pagination) {
	$nav .= elgg_view('navigation/pagination',array(
		'baseurl' => $baseurl,
		'offset' => $offset,
		'count' => $count,
		'limit' => $limit,
		'uid' => $uid,
	));
}

if ($listtype == 'list') {
	if (is_array($entities) && sizeof($entities) > 0) {
		foreach($entities as $entity) {
			$html .= elgg_view_entity($entity, $fullview);
		}
	}
} else {
	if (is_array($entities) && sizeof($entities) > 0) {
		$html .= elgg_view('entities/gallery', array('entities' => $entities));
	}
}

if ($count) {
	$html .= '</div><div style="clear: both;"></div>' . $nav;
}

$script .= "<script type='text/javascript'>
				$(document).ready(function(){
					$(\"#uberview_entity_list_$uid\").fadeIn('fast');
				});
			</script>";

echo $nav . $html . $script;
