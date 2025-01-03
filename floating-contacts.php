<?php
/**
 * Floating Contacts
 *
 * @package           Floating_Contacts
 * @author            Davison Pro
 * @copyright         2023 Davison Pro
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Floating Contacts
 * Plugin URI:        https://davisonpro.dev/floating-contacts
 * Description:       A customizable floating contact button for your WordPress site.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            Davison Pro
 * Author URI:        https://davisonpro.dev
 * Text Domain:       floating-contacts
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plugin version
define( 'FLOATING_CONTACTS_VERSION', '1.0.0' );
define( 'FLOATING_CONTACTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLOATING_CONTACTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once FLOATING_CONTACTS_PLUGIN_DIR . 'vendor/autoload.php';

register_activation_hook( __FILE__, array( 'Floating_Contacts_Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Floating_Contacts_Activator', 'deactivate' ) );

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 */
function run_floating_contacts() {
	// Check PHP version.
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'floating_contacts_php_version_error' );
		return;
	}

	// Initialize the plugin.
	Floating_Contacts::instance();
}

/**
 * Display an error message if PHP version is too low.
 */
function floating_contacts_php_version_error() {
	$message = sprintf(
		/* translators: %s: PHP version */
		esc_html__( 'Floating Contacts requires PHP version 7.4 or higher. You are running version %s. Please upgrade your PHP version.', 'floating-contacts' ),
		PHP_VERSION
	);

	echo '<div class="notice notice-error"><p>' . $message . '</p></div>';
}

// Run the plugin
add_action( 'plugins_loaded', 'run_floating_contacts' );
