<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      2.0.0
 *
 * @package    DavisonPro\FloatingContacts
 */

namespace DavisonPro\FloatingContacts;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    DavisonPro\FloatingContacts
 * @author     Davison Pro <contact@davisonpro.dev>
 */
class AdminPage {
	/**
	 * The single instance of the class.
	 *
	 * @var AdminPage|null
	 */
	private static ?AdminPage $instance = null;

	/**
	 * AdminPage constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Main AdminPage Instance.
	 *
	 * Ensures only one instance of AdminPage is loaded or can be loaded.
	 *
	 * @return AdminPage Main instance.
	 */
	public static function instance(): AdminPage {
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
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the admin menu item.
	 *
	 * @return void
	 */
	public function add_admin_menu(): void {
		add_options_page(
			__( 'Floating Contacts', 'floating-contacts' ),
			__( 'Floating Contacts', 'floating-contacts' ),
			'manage_options',
			'floating_contacts',
			array( $this, 'render_admin_page' )
		);
	}

	/**
	 * Register plugin settings.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			'floating_contacts_options',
			'floating_contacts_options',
			array(
				'sanitize_callback' => array( $this, 'sanitize_options' ),
			)
		);

		add_settings_section(
			'floating_contacts_general',
			__( 'General Settings', 'floating-contacts' ),
			array( $this, 'render_general_section' ),
			'floating_contacts'
		);
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @param string $hook The current admin page.
	 * @return void
	 */
	public function enqueue_assets( string $hook ): void {
		if ( 'settings_page_floating_contacts' !== $hook ) {
			return;
		}

		wp_enqueue_script( 'wp-util' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'floating-contacts-admin',
			FLOATING_CONTACTS_URL . 'assets/css/admin.css',
			array(),
			FLOATING_CONTACTS_VERSION
		);
	}

	/**
	 * Add options page
	 *
	 * @since    2.0.0
	 */
	public function add_options_page() {
		add_options_page(
			__( 'Floating Contacts Settings', 'floating-contacts' ),
			__( 'Floating Contacts', 'floating-contacts' ),
			'manage_options',
			'floating_contacts',
			array( $this, 'render_options_page' )
		);
	}

	/**
	 * Render the admin page.
	 *
	 * @return void
	 */
	public function render_admin_page(): void {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Render the admin page view.
		require_once FLOATING_CONTACTS_DIR . 'views/admin-page.php';
	}

	/**
	 * Render the general section description.
	 *
	 * @return void
	 */
	public function render_general_section(): void {
		echo '<p>' . esc_html__( 'Configure the general settings for Floating Contacts.', 'floating-contacts' ) . '</p>';
	}

	/**
	 * Sanitize options
	 *
	 * @since    2.0.0
	 * @param    array $input    The input options.
	 * @return   array    The sanitized options.
	 */
	public function sanitize_options( $input ) {
		$sanitized_input = array();
		$default_options = $this->get_default_options();

		// Sanitize boolean options.
		$boolean_options = array( 'phone_enabled', 'email_enabled', 'whatsapp_enabled' );
		foreach ( $boolean_options as $option ) {
			$sanitized_input[ $option ] = isset( $input[ $option ] ) && $input[ $option ] ? true : false;
		}

		// Sanitize text field options.
		$text_field_options = array( 'phone_number', 'whatsapp_number' );
		foreach ( $text_field_options as $option ) {
			$sanitized_input[ $option ] = isset( $input[ $option ] ) ? sanitize_text_field( $input[ $option ] ) : $default_options[ $option ];
		}

		// Sanitize email.
		$sanitized_input['email_address'] = isset( $input['email_address'] ) ? sanitize_email( $input['email_address'] ) : $default_options['email_address'];

		// Sanitize textarea.
		$sanitized_input['whatsapp_message'] = isset( $input['whatsapp_message'] ) ? sanitize_textarea_field( $input['whatsapp_message'] ) : $default_options['whatsapp_message'];

		// Sanitize color.
		$sanitized_input['bg_color'] = isset( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : $default_options['bg_color'];

		// Sanitize position.
		$valid_positions             = array( 'bottom-right', 'bottom-left' );
		$sanitized_input['position'] = isset( $input['position'] ) && in_array( $input['position'], $valid_positions ) ? $input['position'] : $default_options['position'];

		// Sanitize custom links.
		$sanitized_input['custom_links'] = array();
		if ( isset( $input['custom_links'] ) && is_array( $input['custom_links'] ) ) {
			foreach ( $input['custom_links'] as $link ) {
				if ( isset( $link['label'], $link['url'] ) && ! empty( $link['label'] ) && ! empty( $link['url'] ) ) {
					$sanitized_link                    = array(
						'label' => sanitize_text_field( $link['label'] ),
						'url'   => esc_url_raw( $link['url'] ),
						'icon'  => isset( $link['icon'] ) ? sanitize_text_field( $link['icon'] ) : '',
					);
					$sanitized_input['custom_links'][] = $sanitized_link;
				}
			}
		}

		return $sanitized_input;
	}

	/**
	 * Get default options
	 *
	 * @since    2.0.0
	 * @return   array    The default options.
	 */
	private function get_default_options() {
		return array(
			'phone_enabled'    => false,
			'phone_number'     => '',
			'email_enabled'    => false,
			'email_address'    => '',
			'whatsapp_enabled' => false,
			'whatsapp_number'  => '',
			'whatsapp_message' => '',
			'bg_color'         => '#0073aa',
			'position'         => 'bottom-right',
			'custom_links'     => array(),
		);
	}

	/**
	 * Render custom link fields
	 *
	 * @since    2.0.0
	 * @param    int   $index    The index of the custom link.
	 * @param    array $link     The link data.
	 */
	public function render_custom_link_fields( $index, $link = array()) {
		$label = isset( $link['label'] ) ? esc_attr( $link['label'] ) : '';
		$url   = isset( $link['url'] ) ? esc_url( $link['url'] ) : '';
		$icon  = isset( $link['icon'] ) ? esc_attr( $link['icon'] ) : '';
		?>
		<div class="fc-custom-link-item">
			<input type="text" name="floating_contacts_options[custom_links][<?php echo $index; ?>][label]" value="<?php echo $label; ?>" placeholder="<?php esc_attr_e( 'Label', 'floating-contacts' ); ?>" class="fc-input">
			<input type="url" name="floating_contacts_options[custom_links][<?php echo $index; ?>][url]" value="<?php echo $url; ?>" placeholder="<?php esc_attr_e( 'https://', 'floating-contacts' ); ?>" class="fc-input">
			<input type="text" name="floating_contacts_options[custom_links][<?php echo $index; ?>][icon]" value="<?php echo $icon; ?>" placeholder="<?php esc_attr_e( 'Icon class (e.g., fa-facebook)', 'floating-contacts' ); ?>" class="fc-input">
			<button type="button" class="fc-button fc-button-danger remove-custom-link"><?php esc_html_e( 'Remove', 'floating-contacts' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Get plugin options
	 *
	 * @since    2.0.0
	 * @return   array    The plugin options.
	 */
	public function get_options() {
		return get_option( 'floating_contacts_options', array() );
	}
}
