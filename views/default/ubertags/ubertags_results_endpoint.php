<?php

$save_form = elgg_view('ubertags/forms/save', array('search' => $vars['search']));
$content = elgg_view('ubertags/ubertags_list_results', array('search' => $vars['search']));

echo <<<EOT
	<div id='ubertags_save_container' class='hidden'>
		$save_form
	</div>
	<div style='clear: both;'></div>
	$content
EOT;

?>
