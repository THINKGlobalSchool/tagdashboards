<?php

switch ($vars['type']) {
	case 'activity': 
		$content = elgg_view('ubertags/activity_tag', array('search' => $vars['search'], 'subtypes' => $vars['subtypes']));
	break;
	case 'custom': 
		$content = elgg_view('ubertags/custom', array('search' => $vars['search'], 'custom' => $vars['custom'], 'subtypes' => $vars['subtypes']));
	break;
	default: 
	case 'subtype': 
		$content = elgg_view('ubertags/subtypes', array('search' => $vars['search'], 'subtypes' => $vars['subtypes']));
	break;
}


echo <<<EOT
	<div style='clear: both;'></div>
	<div id='ubertags-content'>
		$content
	</div>
EOT;

?>