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
define( 'FLOATING_CONTACTS_VERSION', '1.0.0' );
define( 'FLOATING_CONTACTS_MINIMUM_PHP_VERSION', '7.4' );
define( 'FLOATING_CONTACTS_MINIMUM_WP_VERSION', '5.2' );
define( 'FLOATING_CONTACTS_NAMESPACE', __NAMESPACE__ );
define( 'FLOATING_CONTACTS_BASENAME', plugin_basename( __FILE__ ) );
define( 'FLOATING_CONTACTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'FLOATING_CONTACTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Display missing dependencies error.
 *
 * @return void
 */
function floating_contacts_missing_composer_dependencies() {
	$message = sprintf(
		/* translators: 1: composer command. 2: plugin directory */
		esc_html__( 'Your installation of the Floating Contacts plugin is incomplete. Please run %1$s within the %2$s plugin directory.', 'floating-contacts' ),
		'<code>composer install</code>',
		'<code>' . esc_html( plugin_dir_path() ) . '</code>'
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', wp_kses_post( $message ) );
}

/**
 * Autoload classes.
 */
if ( ! file_exists( FLOATING_CONTACTS_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	add_action( 'admin_notices', 'floating_contacts_missing_composer_dependencies' );
	return;
}

require_once FLOATING_CONTACTS_PLUGIN_DIR . 'vendor/autoload.php';

/**
 * Autoload classes.
 */
require_once FLOATING_CONTACTS_PLUGIN_DIR . 'includes/class-autoloader.php';
$autoloader = new Autoloader( FLOATING_CONTACTS_NAMESPACE, FLOATING_CONTACTS_PLUGIN_DIR );
$autoloader->register();


/**
 * Display PHP version error.
 *
 * @return void
 */
function floating_contacts_php_version_error() {
	$message = sprintf(
		/* translators: 1: Current PHP version 2: Required PHP version */
		esc_html__( 'Floating Contacts requires PHP version %2$s or higher. You are running version %1$s. Please upgrade your PHP version.', 'floating-contacts' ),
		PHP_VERSION,
		FLOATING_CONTACTS_MINIMUM_PHP_VERSION
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * Display WordPress version error.
 *
 * @return void
 */
function floating_contacts_wordpress_version_error() {
	$message = sprintf(
		/* translators: 1: Current WordPress version 2: Required WordPress version */
		esc_html__( 'Floating Contacts requires WordPress version %2$s or higher. You are running version %1$s. Please upgrade WordPress.', 'floating-contacts' ),
		get_bloginfo( 'version' ),
		FLOATING_CONTACTS_MINIMUM_WP_VERSION
	);

	printf( '<div class="notice notice-error"><p>%s</p></div>', esc_html( $message ) );
}

/**
 * Begins execution of the plugin.
 *
 * @since 1.0.0
 * @return void
 */
function floating_contacts_initialize() {
	// Check PHP version.
	if ( version_compare( PHP_VERSION, FLOATING_CONTACTS_MINIMUM_PHP_VERSION, '<' ) ) {
		add_action( 'admin_notices', FLOATING_CONTACTS_NAMESPACE . '\floating_contacts_php_version_error' );
		return;
	}

	// Check WordPress version.
	if ( version_compare( get_bloginfo( 'version' ), FLOATING_CONTACTS_MINIMUM_WP_VERSION, '<' ) ) {
		add_action( 'admin_notices', FLOATING_CONTACTS_NAMESPACE . '\floating_contacts_wordpress_version_error' );
		return;
	}

	// Register activation and deactivation hooks.
	register_activation_hook( __FILE__, array( Activator::class, 'activate' ) );
	register_deactivation_hook( __FILE__, array( Activator::class, 'deactivate' ) );

	// Initialize the plugin.
	Plugin::instance();
}

add_action( 'plugins_loaded', FLOATING_CONTACTS_NAMESPACE . '\floating_contacts_initialize' );
