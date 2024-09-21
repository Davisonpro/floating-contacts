<?php
/**
 * Fired during plugin activation
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      2.0.0
 *
 * @package    DavisonPro\FloatingContacts
 */

namespace DavisonPro\FloatingContacts;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      2.0.0
 * @package    DavisonPro\FloatingContacts
 * @author     Davison Pro <davis@davisonpro.dev>
 */
class Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function activate() {
		self::add_version_info();
		self::set_default_options();

		// Trigger action for other plugins/themes.
		do_action( 'floating_contacts_activated' );
	}

	/**
	 * Adds or updates the plugin version in the options table.
	 *
	 * @since    2.0.0
	 */
	private static function add_version_info() {
		$installed_version = get_option( 'floating_contacts_version' );

		if ( ! $installed_version ) {
			add_option( 'floating_contacts_version', FLOATING_CONTACTS_VERSION );
		} else {
			update_option( 'floating_contacts_version', FLOATING_CONTACTS_VERSION );
		}

		// Add installation date
		if ( ! get_option( 'floating_contacts_install_date' ) ) {
			add_option( 'floating_contacts_install_date', current_time( 'mysql' ) );
		}
	}

	/**
	 * Sets default options for the plugin.
	 *
	 * @since    2.0.0
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

		// Only add the option if it doesn't already exist.
		if ( ! get_option( 'floating_contacts_options' ) ) {
			add_option( 'floating_contacts_options', $default_options );
		}
	}
}
