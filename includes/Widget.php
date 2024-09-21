<?php
/**
 * Frontend Widget Handler
 *
 * @package DavisonPro\FloatingContacts
 */

namespace DavisonPro\FloatingContacts;

/**
 * Class Widget
 *
 * Handles the frontend widget functionality for the Floating Contacts plugin.
 */
class Widget {
	/**
	 * The single instance of the class.
	 *
	 * @var Widget|null
	 */
	private static ?Widget $instance = null;

	/**
	 * Widget constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Main Widget Instance.
	 *
	 * Ensures only one instance of Widget is loaded or can be loaded.
	 *
	 * @return Widget Main instance.
	 */
	public static function instance(): Widget {
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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render' ) );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @return void
	 */
	public function enqueue_assets(): void {
		wp_enqueue_style(
			'floating-contacts-frontend',
			FLOATING_CONTACTS_URL . 'assets/css/widget.css',
			array(),
			FLOATING_CONTACTS_VERSION
		);

		wp_enqueue_script(
			'floating-contacts-frontend',
			FLOATING_CONTACTS_URL . 'assets/js/widget.js',
			array( 'jquery' ),
			FLOATING_CONTACTS_VERSION,
			true
		);
	}

	/**
	 * Render the floating contacts widget.
	 *
	 * @return void
	 */
	public function render(): void {
		$settings = get_option( 'floating_contacts_options', array() );

		if ( empty( array_filter( $settings ) ) ) {
			return;
		}

		// Render the frontend widget view.
		require_once FLOATING_CONTACTS_DIR . 'views/floating-widget.php';
	}
}
