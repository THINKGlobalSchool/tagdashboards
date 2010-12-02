<?php
	/**
	 * Ubertags start.pjp
	 * 
	 * @package Ubertags
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Jeff Tilson
	 * @copyright THINK Global School 2010
	 * @link http://www.thinkglobalschool.com/
	 * 
	 */
	
	function ubertags_init() {
		global $CONFIG;
				
		// Extend CSS
		elgg_extend_view('css','ubertags/css');
		
		// Page handler
		register_page_handler('ubertags','ubertags_page_handler');

		// Add to tools menu
		add_menu(elgg_echo("ubertags"), $CONFIG->wwwroot . 'pg/ubertags');

		// Add submenus
		register_elgg_event_handler('pagesetup','system','ubertags_submenus');
						
		// Set up url handlers
		register_entity_url_handler('ubertag_url','object', 'ubertag');

		// Register actions
		register_action('ubertags/create', false, $CONFIG->pluginspath . 'ubertags/actions/create.php');
	
		// Register type
		register_entity_type('object', 'ubertag');		

		return true;
		
	}

	/* Ubertags page handler */
	function ubertags_page_handler($page) {
		global $CONFIG;
		
		
		return true;
	}
		
	/**
	 * Setup ubertags submenus
	 */
	function ubertags_submenus() {
		global $CONFIG;
		$page_owner = elgg_get_page_owner();
		// none.. 
	}
		
	/**
	 * Populates the ->getUrl() method for an ubertag
	 *
	 * @param ElggEntity entity
	 * @return string request url
	 */
	function ubertag_url($entity) {
		global $CONFIG;
		return $CONFIG->url . "pg/ubertags/view/{$entity->guid}/";
	}
	
	register_elgg_event_handler('init', 'system', 'ubertags_init');
?>