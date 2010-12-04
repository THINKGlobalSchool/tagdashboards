<?php
/**
 * Ubertags delete action
 *
 * @package Ubertags
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Jeff Tilson
 * @copyright THINK Global School 2010
 * @link http://www.thinkglobalschool.org
 */

$guid = get_input('guid', null);
$ubertag = get_entity($guid);

if (elgg_instanceof($ubertag, 'object', 'ubertag') && $ubertag->canEdit()) {
	$container = get_entity($ubertag->container_guid);
	if ($ubertag->delete()) {
		system_message(elgg_echo('ubertags:success:delete'));
		forward("pg/ubertags/{$container->username}");
	} else {
		register_error(elgg_echo('ubertags:error:delete'));
	}
} else {
	register_error(elgg_echo('ubertags:error:notfound'));
}

forward(REFERER);