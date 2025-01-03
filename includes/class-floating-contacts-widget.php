<?php
/**
 * Frontend Widget Handler
 *
 * @package    Floating_Contacts
 * @subpackage Frontend
 * @since      1.0.0
 */

/**
 * Class Floating_Contacts_Widget
 *
 * Handles the frontend widget functionality for the Floating Contacts plugin.
 * Implements singleton pattern for performance and single instance management.
 *
 * @since 1.0.0
 */
class Floating_Contacts_Widget {

	/**
	 * The single instance of the class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Floating_Contacts_Widget|null
	 */
	private static $instance = null;

	/**
	 * Plugin settings.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    array
	 */
	private $settings = array();

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * Singleton via the `new` operator from outside this class.
	 *
	 * @since  1.0.0
	 * @access private
	 */
	private function __construct() {
		$this->settings = get_option( 'floating_contacts_options', array() );
		$this->init();
	}

	/**
	 * Main Widget Instance.
	 *
	 * Ensures only one instance of Widget is loaded or can be loaded.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return Floating_Contacts_Widget Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the widget functionality.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function init() {
		if ( ! $this->should_load() ) {
			return;
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'wp_footer', array( $this, 'render' ) );
	}

	/**
	 * Check if widget should be loaded.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return bool
	 */
	private function should_load() {
		if ( empty( array_filter( $this->settings ) ) ) {
			return false;
		}

		// Allow developers to prevent widget loading.
		return apply_filters( 'floating_contacts_should_load', true );
	}

	/**
	 * Enqueue frontend assets.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function enqueue_assets() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// only load FA fonts if custom links are present.
		if ( ! empty( $this->settings['custom_links'] ) ) {
			wp_enqueue_style(
				'floating-contacts-fontawesome',
				'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css',
				array(),
				'5.15.3'
			);
		}

		wp_enqueue_style(
			'floating-contacts-frontend',
			FLOATING_CONTACTS_PLUGIN_URL . "assets/css/widget{$suffix}.css",
			array(),
			FLOATING_CONTACTS_VERSION
		);

		wp_enqueue_script(
			'floating-contacts-frontend',
			FLOATING_CONTACTS_PLUGIN_URL . "assets/js/widget{$suffix}.js",
			array( 'jquery' ),
			FLOATING_CONTACTS_VERSION,
			true
		);
	}

	/**
	 * Render the floating contacts widget.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function render() {
		$position_class = 'FloatingContacts--' . ( $this->settings['position'] ?? 'bottom-right' );
		$inline_styles  = $this->get_inline_styles();
		?>
		<div class="FloatingContacts <?php echo esc_attr( $position_class ); ?>"
			style="<?php echo esc_attr( $inline_styles ); ?>"
			aria-label="<?php esc_attr_e( 'Floating Contact Buttons', 'floating-contacts' ); ?>">
			<?php
			$this->render_button();
			$this->render_contact_list();
			?>
		</div>
		<?php
	}

	/**
	 * Get inline styles for the widget.
	 *
	 * @return string
	 */
	private function get_inline_styles() {
		$bg_color    = $this->settings['bg_color'] ?? '#1e88e5';
		$hover_color = $this->adjust_brightness( $bg_color, 20 );
		return "--fc-bg-color: {$bg_color}; --fc-hover-color: {$hover_color};";
	}

	/**
	 * Render the main button.
	 */
	private function render_button() {
		$enabled_contacts = count(
			array_filter(
				array(
					$this->settings['phone_enabled'] ?? false,
					$this->settings['email_enabled'] ?? false,
					$this->settings['whatsapp_enabled'] ?? false,
				)
			)
		);

		$total_icons = $enabled_contacts + count( $this->settings['custom_links'] ?? array() );
		?>
		<button type="button" class="FloatingContacts__button" aria-expanded="false" aria-controls="FloatingContacts-list">
			<span class="FloatingContacts__button-close">
				<span data-icon="dots"><i></i><i></i><i></i></span>
			</span>
			<span class="FloatingContacts__button-icons" data-icons-number="<?php echo esc_attr( $total_icons ); ?>">
				<span data-icon="dots"><i></i><i></i><i></i></span>
				<?php $this->render_contact_icons(); ?>
			</span>
		</button>
		<?php
	}

	/**
	 * Render contact icons.
	 */
	private function render_contact_icons() {
		$icons = array(
			'phone'    => ! empty( $this->settings['phone_enabled'] ),
			'email'    => ! empty( $this->settings['email_enabled'] ),
			'whatsapp' => ! empty( $this->settings['whatsapp_enabled'] ),
		);

		foreach ( $icons as $icon => $enabled ) {
			if ( $enabled ) {
				echo '<span data-icon="' . esc_attr( $icon ) . '"></span>';
			}
		}

		if ( ! empty( $this->settings['custom_links'] ) ) {
			foreach ( $this->settings['custom_links'] as $link ) {
				echo '<span data-icon="' . esc_attr( $link['icon'] ) . '"></span>';
			}
		}
	}

	/**
	 * Render the contact list.
	 */
	private function render_contact_list() {
		?>
		<div id="FloatingContacts-list" class="FloatingContacts__list" aria-label="<?php esc_attr_e( 'Contact Options', 'floating-contacts' ); ?>">
			<?php
			$this->render_phone_contact();
			$this->render_email_contact();
			$this->render_whatsapp_contact();
			$this->render_custom_links();
			?>
		</div>
		<?php
	}

	/**
	 * Render phone contact.
	 */
	private function render_phone_contact() {
		if ( ! empty( $this->settings['phone_enabled'] ) && ! empty( $this->settings['phone_number'] ) ) {
			?>
			<a href="tel:<?php echo esc_attr( $this->settings['phone_number'] ); ?>" class="FloatingContacts__list-item" data-icon="phone" rel="nofollow">
				<?php echo esc_html( $this->settings['phone_number'] ); ?>
			</a>
			<?php
		}
	}

	/**
	 * Render email contact.
	 */
	private function render_email_contact() {
		if ( ! empty( $this->settings['email_enabled'] ) && ! empty( $this->settings['email_address'] ) ) {
			?>
			<a href="mailto:<?php echo esc_attr( $this->settings['email_address'] ); ?>" class="FloatingContacts__list-item" data-icon="email" rel="nofollow">
				<?php esc_html_e( 'Email', 'floating-contacts' ); ?>
			</a>
			<?php
		}
	}

	/**
	 * Render WhatsApp contact.
	 */
	private function render_whatsapp_contact() {
		if ( ! empty( $this->settings['whatsapp_enabled'] ) && ! empty( $this->settings['whatsapp_number'] ) ) {
			$whatsapp_url = $this->build_whatsapp_url();
			?>
			<a href="<?php echo esc_url( $whatsapp_url ); ?>" class="FloatingContacts__list-item" data-icon="whatsapp" rel="nofollow noopener" target="_blank">
				<?php esc_html_e( 'WhatsApp', 'floating-contacts' ); ?>
			</a>
			<?php
		}
	}

	/**
	 * Build WhatsApp URL.
	 *
	 * @return string
	 */
	private function build_whatsapp_url() {
		$base_url = 'https://api.whatsapp.com/send/?phone=' . preg_replace( '/[^0-9]/', '', $this->settings['whatsapp_number'] );
		$query    = array( 'app_absent' => '0' );

		if ( ! empty( $this->settings['whatsapp_message'] ) ) {
			$query['text'] = $this->settings['whatsapp_message'];
		}

		return $base_url . '&' . http_build_query( $query );
	}

	/**
	 * Render custom links.
	 */
	private function render_custom_links() {
		if ( ! empty( $this->settings['custom_links'] ) ) {
			foreach ( $this->settings['custom_links'] as $link ) {
				$icon_class = ! empty( $link['icon'] ) ? $link['icon'] : 'fab fa-link';
				?>
				<a href="<?php echo esc_url( $link['url'] ); ?>" class="FloatingContacts__list-item" rel="nofollow noopener" target="_blank">
					<i class="FloatingContacts__link-icon <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
					<span class="FloatingContacts__link-label"><?php echo esc_html( $link['label'] ); ?></span>
				</a>
				<?php
			}
		}
	}

	/**
	 * Adjust color brightness
	 *
	 * @param string $hex   Hex color code
	 * @param int    $steps Steps to darken or lighten (negative to darken)
	 * @return string Adjusted hex color code
	 */
	public function adjust_brightness( $hex, $steps ) {
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
