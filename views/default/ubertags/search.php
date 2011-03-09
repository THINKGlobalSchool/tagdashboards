<?php

$save_form = elgg_view('forms/ubertags/save', array('search' => $vars['search']));
$content = elgg_view('ubertags/content', array('search' => $vars['search'], 'group_by' => UBERTAGS_GROUP_SUBTYPE));

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