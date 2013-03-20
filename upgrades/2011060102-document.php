<?php
/**
 * Disable files subtype and enable documents
 */

$subtypes = tagdashboards_get_enabled_subtypes();

// Disable documents and enable files in settings
foreach ($subtypes as $idx => $type) {
	if ($type == 'document') {
		unset($subtypes[$idx]);
		$subtypes[] = 'file';
	}
}

elgg_set_plugin_setting('enabled_subtypes', serialize($subtypes), 'tagdashboards');

// Grab all tagdashboards
$tagdashboards = elgg_get_entities(array(
	'type' => 'object',
	'subtype' => 'tagdashboard',
	'limit' => 0,
));

// Disable documents and enable files for all dashboards
foreach ($tagdashboards as $dashboard) {
	$dashboard_subtypes = unserialize($dashboard->subtypes);
	foreach ($dashboard_subtypes as $idx => $type) {
		if ($type == 'document') {
			unset($dashboard_subtypes[$idx]);
			$dashboard_subtypes[] = 'file';
			$dashboard->subtypes = serialize($dashboard_subtypes);
			$dashboard->save();
		}
	}
}