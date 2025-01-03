<?php
/**
 * Floating Contacts
 *
 * @package           Floating_Contacts
 * @author            Davison Pro
 * @copyright         2025 Davison Pro
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
 * Domain Path:       /languages
 * Stable Tag:        1.0.0
 */

namespace Floating_Contacts;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define plugin constants.
define( __NAMESPACE__ . '\VERSION', '1.0.0' );
define( __NAMESPACE__ . '\MINIMUM_PHP_VERSION', '7.4' );
define( __NAMESPACE__ . '\MINIMUM_WP_VERSION', '5.2' );
define( __NAMESPACE__ . '\PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( __NAMESPACE__ . '\PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Display missing dependencies error.
 *
 * @return void
 */
function missing_composer_dependencies() {
	$message = sprintf(
		/* translators: 1: composer command. 2: plugin directory */
		esc_html__( 'Your installation of the Floating Contacts plugin is incomplete. Please run %1$s within the %2$s plugin directory.', 'floating-contacts' ),
		'<code>composer install</code>',
		'<code>' . esc_html( str_replace( ABSPATH, '', PLUGIN_DIR ) ) . '</code>'
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
}

/**
 * Autoload classes.
 */
if ( ! file_exists( PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	add_action( 'admin_notices', __NAMESPACE__ . '\missing_composer_dependencies' );
	return;
}

require_once PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Autoload classes.
 */
require_once PLUGIN_DIR . 'includes/class-autoloader.php';
$autoloader = new Autoloader( __NAMESPACE__, PLUGIN_DIR );
$autoloader->register();


/**
 * Display PHP version error.
 *
 * @return void
 */
function php_version_error() {
	$message = sprintf(
		/* translators: 1: Current PHP version 2: Required PHP version */
		esc_html__( 'Floating Contacts requires PHP version %2$s or higher. You are running version %1$s. Please upgrade your PHP version.', 'floating-contacts' ),
		PHP_VERSION,
		MINIMUM_PHP_VERSION
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function initialize() {
	// Check PHP version.
	if ( version_compare( PHP_VERSION, MINIMUM_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\php_version_error' );
		return;
	}

	// Check WordPress version.
	if ( version_compare( get_bloginfo( 'version' ), MINIMUM_WP_VERSION, '<' ) ) {
		add_action( 'admin_notices', __NAMESPACE__ . '\wordpress_version_error' );
		return;
	}

	// Register activation and deactivation hooks.
	register_activation_hook( __FILE__, array( 'Floating_Contacts\Activator', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Floating_Contacts\Activator', 'deactivate' ) );

	// Initialize the plugin.
	Plugin::instance();
}

add_action( 'plugins_loaded', __NAMESPACE__ . '\initialize' );
