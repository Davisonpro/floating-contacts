<?php
/**
 * The plugin bootstrap file
 *
 * @link              https://davisonpro.dev/floating-contacts
 * @since             2.0.0
 * @package           DavisonPro\FloatingContacts
 *
 * @wordpress-plugin
 * Plugin Name:       Floating Contacts
 * Plugin URI:        https://davisonpro.dev/floating-contacts
 * Description:       A customizable floating contact button for your WordPress site.
 * Version:           2.0.0
 * Author:            Davison Pro
 * Author URI:        https://davisonpro.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       floating-contacts
 * Domain Path:       /languages
 * Requires PHP:      7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plugin version
define( 'FLOATING_CONTACTS_VERSION', '2.0.0' );

// Plugin directory
define( 'FLOATING_CONTACTS_DIR', plugin_dir_path( __FILE__ ) );

// Plugin URL
define( 'FLOATING_CONTACTS_URL', plugin_dir_url( __FILE__ ) );

require_once FLOATING_CONTACTS_DIR . 'vendor/autoload.php';

register_activation_hook( __FILE__, 'DavisonPro\FloatingContacts\Activator::activate' );
register_deactivation_hook( __FILE__, 'DavisonPro\FloatingContacts\Deactivator::deactivate' );

/**
 * Begins execution of the plugin.
 *
 * @since    2.0.0
 */
function run_floating_contacts(): void {
	// Check PHP version.
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		add_action( 'admin_notices', 'floating_contacts_php_version_error' );
		return;
	}

	// Run the plugin.
	$plugin = DavisonPro\FloatingContacts\FloatingContacts::instance();
	$plugin->init();
}

/**
 * Display an error message if PHP version is too low.
 */
function floating_contacts_php_version_error(): void {
	$class   = 'notice notice-error';
	$message = sprintf(
		__( 'Floating Contacts requires PHP version 7.4 or higher. You are running version %s. Please upgrade your PHP version.', 'floating-contacts' ),
		PHP_VERSION
	);
	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
}

// Run the plugin
add_action( 'plugins_loaded', 'run_floating_contacts' );
