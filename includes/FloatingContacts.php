<?php
/**
 * Main FloatingContacts class
 *
 * @package DavisonPro\FloatingContacts
 */

namespace DavisonPro\FloatingContacts;

use DavisonPro\FloatingContacts\AdminPage;
use DavisonPro\FloatingContacts\Widget;

/**
 * Class FloatingContacts
 *
 * Handles the core functionality of the Floating Contacts plugin.
 */
class FloatingContacts {
	/**
	 * The single instance of the class.
	 *
	 * @var FloatingContacts|null
	 */
	private static ?FloatingContacts $instance = null;

	/**
	 * FloatingContacts constructor.
	 */
	private function __construct() {
		AdminPage::instance()->init();
		Widget::instance()->init();
	}

	/**
	 * Main FloatingContacts Instance.
	 *
	 * Ensures only one instance of FloatingContacts is loaded or can be loaded.
	 *
	 * @return FloatingContacts Main instance.
	 */
	public static function instance(): FloatingContacts {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->load_dependencies();
		$this->set_locale();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @return void
	 */
	private function load_dependencies(): void {
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * @return void
	 */
	private function set_locale(): void {
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain(): void {
		load_plugin_textdomain(
			'floating-contacts',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Adjust color brightness
	 *
	 * @param string $hex Hex color code
	 * @param int    $steps Steps to darken or lighten (negative to darken)
	 * @return string Adjusted hex color code
	 */
	public static function adjust_brightness( $hex, $steps ) {
		// Remove # if present.
		$hex = ltrim( $hex, '#' );

		// Convert to RGB.
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );

		// Adjust brightness.
		$r = max( 0, min( 255, $r + $steps ) );
		$g = max( 0, min( 255, $g + $steps ) );
		$b = max( 0, min( 255, $b + $steps ) );

		// Convert back to hex.
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
}
