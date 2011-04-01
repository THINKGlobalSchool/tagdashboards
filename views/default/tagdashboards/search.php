<?php

switch ($vars['type']) {
	case 'activity': 
		$content = elgg_view('tagdashboards/activity_tag', array('search' => $vars['search'], 'subtypes' => $vars['subtypes']));
	break;
	case 'custom': 
		$content = elgg_view('tagdashboards/custom', array('search' => $vars['search'], 'custom' => $vars['custom'], 'subtypes' => $vars['subtypes']));
	break;
	default: 
	case 'subtype': 
		$content = elgg_view('tagdashboards/subtypes', array('search' => $vars['search'], 'subtypes' => $vars['subtypes']));
	break;
}


echo <<<HTML
	<div style='clear: both;'></div>
	<div id='tagdashboards-content'>
		$content
	</div>
HTML;

?>