<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      1.0.0
 *
 * @package    Floating_Contacts
 */

namespace Floating_Contacts;

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Floating_Contacts
 * @author     Davison Pro <davis@davisonpro.dev>
 */
class Admin_Page {
	/**
	 * The single instance of the class.
	 *
	 * @var Admin_Page|null
	 */
	private static $instance = null;

	/**
	 * Admin_Page constructor.
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Main Admin_Page Instance.
	 *
	 * Ensures only one instance of Admin_Page is loaded or can be loaded.
	 *
	 * @return Admin_Page Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin.
	 */
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Register the admin menu item.
	 */
	public function add_admin_menu() {
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
	 */
	public function register_settings() {
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
	 */
	public function enqueue_assets( $hook ) {
		if ( 'settings_page_floating_contacts' !== $hook ) {
			return;
		}

		$suffix  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$options = $this->get_options();

		wp_enqueue_script( 'wp-util' );
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );

		wp_enqueue_style(
			'floating-contacts-fontawesome',
			PLUGIN_URL . 'assets/libs/font-awesome/css/all.min.css',
			array(),
			'6.7.2'
		);

		wp_enqueue_style(
			'floating-contacts-admin',
			PLUGIN_URL . "assets/css/admin{$suffix}.css",
			array(),
			VERSION
		);

		wp_enqueue_script(
			'floating-contacts-admin',
			PLUGIN_URL . "assets/js/admin{$suffix}.js",
			array( 'jquery', 'wp-color-picker', 'wp-util' ),
			VERSION,
			true
		);

		wp_localize_script(
			'floating-contacts-admin',
			'FC_Admin',
			array(
				'totalCustomLinks' => (int) count( $options['custom_links'] ?? array() ),
			)
		);
	}

	/**
	 * Add options page
	 *
	 * @since 1.0.0
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
	 */
	public function render_admin_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$options      = get_option( 'floating_contacts_options', array() );
		$custom_links = $options['custom_links'] ?? array();
		?>

		<div class="wrap floating-contacts-admin">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php settings_errors( 'floating_contacts_messages' ); ?>

			<form method="post" action="options.php" id="floating-contacts-settings-form">
				<?php
				settings_fields( 'floating_contacts_options' );
				do_settings_sections( 'floating_contacts' );
				?>
				
				<div class="fc-card">
					<h2 class="fc-card-title"><?php esc_html_e( 'Contact Methods', 'floating-contacts' ); ?></h2>
					
					<div class="fc-field-group">
						<label class="fc-checkbox-label" for="floating_contacts_phone_enabled">
							<input type="checkbox" id="floating_contacts_phone_enabled" name="floating_contacts_options[phone_enabled]" <?php checked( $options['phone_enabled'] ?? false ); ?> class="fc-checkbox">
							<span class="fc-checkbox-text"><?php esc_html_e( 'Enable Phone', 'floating-contacts' ); ?></span>
						</label>
						<input type="tel" id="floating_contacts_phone_number" name="floating_contacts_options[phone_number]" value="<?php echo esc_attr( $options['phone_number'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Phone Number', 'floating-contacts' ); ?>" class="fc-input">
					</div>

					<div class="fc-field-group">
						<label class="fc-checkbox-label" for="floating_contacts_email_enabled">
							<input type="checkbox" id="floating_contacts_email_enabled" name="floating_contacts_options[email_enabled]" <?php checked( $options['email_enabled'] ?? false ); ?> class="fc-checkbox">
							<span class="fc-checkbox-text"><?php esc_html_e( 'Enable Email', 'floating-contacts' ); ?></span>
						</label>
						<input type="email" id="floating_contacts_email_address" name="floating_contacts_options[email_address]" value="<?php echo esc_attr( $options['email_address'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Email Address', 'floating-contacts' ); ?>" class="fc-input">
					</div>

					<div class="fc-field-group">
						<label class="fc-checkbox-label" for="floating_contacts_whatsapp_enabled">
							<input type="checkbox" id="floating_contacts_whatsapp_enabled" name="floating_contacts_options[whatsapp_enabled]" <?php checked( $options['whatsapp_enabled'] ?? false ); ?> class="fc-checkbox">
							<span class="fc-checkbox-text"><?php esc_html_e( 'Enable WhatsApp', 'floating-contacts' ); ?></span>
						</label>
						<input type="text" id="floating_contacts_whatsapp_number" name="floating_contacts_options[whatsapp_number]" value="<?php echo esc_attr( $options['whatsapp_number'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'WhatsApp Number', 'floating-contacts' ); ?>" class="fc-input">
						<br></br>
						<textarea id="floating_contacts_whatsapp_message" name="floating_contacts_options[whatsapp_message]" placeholder="<?php esc_attr_e( 'WhatsApp Message', 'floating-contacts' ); ?>" class="fc-textarea"><?php echo esc_textarea( $options['whatsapp_message'] ?? '' ); ?></textarea>
					</div>
				</div>

				<div class="fc-card">
					<h2 class="fc-card-title"><?php esc_html_e( 'Custom Links', 'floating-contacts' ); ?></h2>
					
					<div id="floating-contacts-custom-links">
						<?php
						if ( ! empty( $custom_links ) ) {
							foreach ( $custom_links as $index => $link ) {
								$this->render_custom_link_fields( $index, $link );
							}
						} else {
							$this->render_custom_link_fields( 0 );
						}
						?>
					</div>
					<p class="description">
						<?php
						printf(
							/* translators: %s: URL to Font Awesome icons */
							esc_html__( 'Enter the Font Awesome icon name (e.g., fa-facebook). See the full list of icons %s.', 'floating-contacts' ),
							'<a href="https://fontawesome.com/icons?d=gallery&m=free" target="_blank" rel="noopener noreferrer">' . esc_html__( 'here', 'floating-contacts' ) . '</a>'
						);
						?>
					</p>
					<button type="button" class="fc-button fc-button-secondary" id="add-custom-link"><?php esc_html_e( 'Add Custom Link', 'floating-contacts' ); ?></button>
				</div>

				<div class="fc-card">
					<h2 class="fc-card-title"><?php esc_html_e( 'Appearance', 'floating-contacts' ); ?></h2>
					
					<div class="fc-field-group">
						<label class="fc-label" for="floating_contacts_bg_color"><?php esc_html_e( 'Background Color', 'floating-contacts' ); ?></label>
						<input type="text" id="floating_contacts_bg_color" name="floating_contacts_options[bg_color]" value="<?php echo esc_attr( $options['bg_color'] ?? '#1e88e5' ); ?>" class="floating-contacts-color-field">
					</div>
					<div class="fc-field-group">
						<label class="fc-label" for="floating_contacts_position"><?php esc_html_e( 'Position', 'floating-contacts' ); ?></label>
						<select id="floating_contacts_position" name="floating_contacts_options[position]" class="fc-select">
							<option value="bottom-right" <?php selected( $options['position'] ?? 'bottom-right', 'bottom-right' ); ?>><?php esc_html_e( 'Bottom Right', 'floating-contacts' ); ?></option>
							<option value="bottom-left" <?php selected( $options['position'] ?? 'bottom-right', 'bottom-left' ); ?>><?php esc_html_e( 'Bottom Left', 'floating-contacts' ); ?></option>
						</select>
					</div>
				</div>

				<?php submit_button( __( 'Save Settings', 'floating-contacts' ), 'button-primary button-large', 'submit', true, array( 'id' => 'fc-submit' ) ); ?>
			</form>
		</div>

		<script type="text/html" id="tmpl-floating-contacts-custom-link">
			<?php $this->render_custom_link_fields( '{{data.index}}' ); ?>
		</script>
		<?php
	}

	/**
	 * Render the general section description.
	 */
	public function render_general_section() {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<p>' . esc_html__( 'Configure the general settings for Floating Contacts.', 'floating-contacts' ) . '</p>';
	}

	/**
	 * Sanitize options
	 *
	 * @since 1.0.0
	 * @param array $input The input options.
	 * @return array The sanitized options.
	 */
	public function sanitize_options( $input ) {
		$sanitized_input = array();
		$default_options = $this->get_default_options();

		// Sanitize boolean options.
		$boolean_options = array( 'phone_enabled', 'email_enabled', 'whatsapp_enabled' );
		foreach ( $boolean_options as $option ) {
			$sanitized_input[ $option ] = isset( $input[ $option ] ) && $input[ $option ];
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
		$sanitized_input['bg_color'] = isset( $input['bg_color'] ) && ! empty( $input['bg_color'] ) ? sanitize_hex_color( $input['bg_color'] ) : $default_options['bg_color'];

		// Sanitize position.
		$valid_positions             = array( 'bottom-right', 'bottom-left' );
		$sanitized_input['position'] = isset( $input['position'] ) && in_array( $input['position'], $valid_positions, true ) ? $input['position'] : $default_options['position'];

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
	 * @since 1.0.0
	 * @return array The default options.
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
			'bg_color'         => '#1e88e5',
			'position'         => 'bottom-right',
			'custom_links'     => array(),
		);
	}

	/**
	 * Render custom link fields
	 *
	 * @since 1.0.0
	 * @param int   $index The index of the custom link.
	 * @param array $link  The link data.
	 */
	public function render_custom_link_fields( $index, $link = array() ) {
		$label = isset( $link['label'] ) ? $link['label'] : '';
		$url   = isset( $link['url'] ) ? $link['url'] : '';
		$icon  = isset( $link['icon'] ) ? $link['icon'] : '';
		?>
		<div class="fc-custom-link-item">
			<input type="text" name="floating_contacts_options[custom_links][<?php echo esc_attr( $index ); ?>][label]" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'Label', 'floating-contacts' ); ?>" class="fc-input" autocomplete="off">
			<input type="url" name="floating_contacts_options[custom_links][<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" placeholder="<?php esc_attr_e( 'https://', 'floating-contacts' ); ?>" class="fc-input" autocomplete="off">
			<input type="text" name="floating_contacts_options[custom_links][<?php echo esc_attr( $index ); ?>][icon]" value="<?php echo esc_attr( $icon ); ?>" placeholder="<?php esc_attr_e( 'fa-icon-name', 'floating-contacts' ); ?>" class="fc-input fc-icon-input" autocomplete="off">
			<span class="fc-icon-preview"></span>
			<button type="button" class="fc-button fc-button-danger remove-custom-link"><?php esc_html_e( 'Remove', 'floating-contacts' ); ?></button>
		</div>
		<?php
	}

	/**
	 * Get plugin options
	 *
	 * @since 1.0.0
	 * @return array The plugin options.
	 */
	public function get_options() {
		return get_option( 'floating_contacts_options', array() );
	}
}
