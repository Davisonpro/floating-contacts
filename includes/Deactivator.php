<?php
/**
 * Fired during plugin deactivation
 *
 * @link       https://davisonpro.dev/floating-contacts
 * @since      2.0.0
 *
 * @package    DavisonPro\FloatingContacts
 */

namespace DavisonPro\FloatingContacts;

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      2.0.0
 * @package    DavisonPro\FloatingContacts
 * @author     Davison Pro <davis@davisonpro.dev>
 */
class Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    2.0.0
	 */
	public static function deactivate() {
		self::clear_scheduled_hooks();
		self::remove_transients();
		self::maybe_remove_data();

		// Trigger action for other plugins/themes.
		do_action( 'floating_contacts_deactivated' );
	}

	/**
	 * Clear any scheduled hooks.
	 *
	 * @since    2.0.0
	 */
	private static function clear_scheduled_hooks() {
		$timestamp = wp_next_scheduled( 'floating_contacts_daily_event' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'floating_contacts_daily_event' );
		}
	}

	/**
	 * Remove any transients set by the plugin.
	 *
	 * @since    2.0.0
	 */
	private static function remove_transients() {
		delete_transient( 'floating_contacts_activation_redirect' );
		// Add any other transients that need to be removed
	}

	/**
	 * Optionally remove plugin data based on user settings.
	 *
	 * @since    2.0.0
	 */
	private static function maybe_remove_data() {
		$remove_data = get_option( 'floating_contacts_remove_data_on_deactivation', false );

		if ( $remove_data ) {
			self::remove_options();
			self::remove_custom_tables();
		}
	}

	/**
	 * Remove all plugin options.
	 *
	 * @since    2.0.0
	 */
	private static function remove_options() {
		delete_option( 'floating_contacts_options' );
		delete_option( 'floating_contacts_version' );
		delete_option( 'floating_contacts_install_date' );
	}

	/**
	 * Remove any custom tables created by the plugin.
	 *
	 * @since    2.0.0
	 */
	private static function remove_custom_tables() {
		global $wpdb;

	}
}
