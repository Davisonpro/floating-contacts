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
		$this->init_hooks();
		$this->load_dependencies();
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
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function load_dependencies() {
		Floating_Contacts_Admin_Page::instance();
		Floating_Contacts_Widget::instance();
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
