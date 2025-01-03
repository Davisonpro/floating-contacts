<?php
/**
 * Main Floating_Contacts class
 *
 * @package Floating_Contacts
 */

/**
 * Class Floating_Contacts
 *
 * Handles the core functionality of the Floating Contacts plugin.
 */
class Floating_Contacts {
	/**
	 * The single instance of the class.
	 *
	 * @var Floating_Contacts|null
	 */
	private static $instance = null;

	/**
	 * Floating_Contacts constructor.
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );

		Floating_Contacts_Admin_Page::instance();
		Floating_Contacts_Widget::instance();
	}

	/**
	 * Main Floating_Contacts Instance.
	 *
	 * Ensures only one instance of Floating_Contacts is loaded or can be loaded.
	 *
	 * @return Floating_Contacts Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'floating-contacts',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}
}
