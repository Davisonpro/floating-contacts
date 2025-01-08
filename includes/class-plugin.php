<?php
/**
 * Main Plugin class
 *
 * @package Plugin
 */

namespace Floating_Contacts;

/**
 * Class Plugin
 *
 * Handles the core functionality of the Floating Contacts plugin.
 */
class Plugin {
	/**
	 * The single instance of the class.
	 *
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Plugin constructor.
	 */
	private function __construct() {
		$this->load_dependencies();
	}

	/**
	 * Main Plugin Instance.
	 *
	 * Ensures only one instance of Plugin is loaded or can be loaded.
	 *
	 * @return Plugin Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		Admin_Page::instance();
		Widget::instance();
	}
}
