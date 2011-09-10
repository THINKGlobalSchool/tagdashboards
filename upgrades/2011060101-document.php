<?php
/**
 * Disable files subtype and enable documents
 */

$subtypes = tagdashboards_get_enabled_subtypes();

foreach ($subtypes as $idx => $type) {
	if ($type == 'document') {
		unset($subtypes[$idx]);
		$subtypes[] = 'file';
	}
}

elgg_set_plugin_setting('enabled_subtypes', serialize($subtypes), 'tagdashboards');
