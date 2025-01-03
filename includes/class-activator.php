<?php
/**
 * Fired during plugin activation and deactivation
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      1.0.0
 * @package    Floating_Contacts
 */

namespace Floating_Contacts;

/**
 * Plugin activation and deactivation handler.
 *
 * This class defines all code necessary to run during the plugin's activation and deactivation.
 *
 * @since      1.0.0
 * @package    Floating_Contacts
 * @author     Davison Pro <davis@davisonpro.dev>
 */
class Activator {

	/**
	 * Handle plugin activation tasks.
	 *
	 * Sets up default options, version information, and triggers activation hooks.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function activate() {
		self::add_version_info();
		self::set_default_options();

		/**
		 * Action hook fired after plugin activation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'floating_contacts_activated' );
	}

	/**
	 * Handle plugin deactivation tasks.
	 *
	 * Cleans up scheduled tasks, transients, and optionally removes plugin data.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public static function deactivate() {
		delete_option( 'floating_contacts_options' );
		delete_option( 'floating_contacts_version' );
		delete_option( 'floating_contacts_install_date' );

		/**
		 * Action hook fired after plugin deactivation.
		 *
		 * @since 1.0.0
		 */
		do_action( 'floating_contacts_deactivated' );
	}

	/**
	 * Add or update plugin version information.
	 *
	 * Stores the current plugin version and installation date in WordPress options.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function add_version_info() {
		$installed_version = get_option( 'floating_contacts_version' );

		if ( ! $installed_version ) {
			add_option( 'floating_contacts_version', FLOATING_CONTACTS_VERSION );
		} else {
			update_option( 'floating_contacts_version', FLOATING_CONTACTS_VERSION );
		}

		if ( ! get_option( 'floating_contacts_install_date' ) ) {
			add_option( 'floating_contacts_install_date', current_time( 'mysql' ) );
		}
	}

	/**
	 * Set default plugin options.
	 *
	 * Initializes the plugin with default settings if they don't exist.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private static function set_default_options() {
		$default_options = array(
			'bg_color'        => '#1e88e5',
			'icon_color'      => '#ffffff',
			'position'        => 'bottom-right',
			'display_on'      => array( 'posts', 'pages' ),
			'contact_methods' => array(
				'phone'    => array(
					'enabled' => true,
					'number'  => '',
					'icon'    => 'phone',
				),
				'email'    => array(
					'enabled' => true,
					'address' => '',
					'icon'    => 'envelope',
				),
				'whatsapp' => array(
					'enabled'            => false,
					'number'             => '',
					'pre_filled_message' => '',
					'icon'               => 'whatsapp',
				),
				'custom'   => array(
					'enabled' => false,
					'title'   => '',
					'url'     => '',
					'icon'    => 'link',
				),
			),
		);

		if ( ! get_option( 'floating_contacts_options' ) ) {
			add_option( 'floating_contacts_options', $default_options );
		}
	}
}
