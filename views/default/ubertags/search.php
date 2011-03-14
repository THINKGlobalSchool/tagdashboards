<?php

switch ($vars['type']) {
	case 'activity': 
		$content = elgg_view('ubertags/activity_tag', array('search' => $vars['search']));
	break;
	case 'custom': 
		$content = elgg_view('ubertags/custom', array('search' => $vars['search'], 'custom' => $vars['custom']));
	break;
	default: 
	case 'subtype': 
		$save_form = elgg_view('forms/ubertags/save', array('search' => $vars['search']));
		$content = elgg_view('ubertags/subtypes', array('search' => $vars['search']));
	break;
}

echo <<<EOT
	<div id='ubertags-save-container' class='hidden'>
		$save_form
	</div>
	<div style='clear: both;'></div>
	<div id='ubertags-content'>
		$content
	</div>
EOT;

?>