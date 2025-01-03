<?php
/**
 * Autoloader for Floating Contacts plugin
 *
 * @package Floating_Contacts
 * @since   1.0.0
 */

namespace Floating_Contacts;

/**
 * Autoloader class for Floating Contacts plugin.
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * Namespace prefix for autoloading.
	 *
	 * @var string
	 */
	private $namespace_prefix;

	/**
	 * Base directory for autoloading.
	 *
	 * @var string
	 */
	private $base_dir;

	/**
	 * PSR-4 prefix to directory mappings.
	 *
	 * @var array
	 */
	private $psr4_mappings = array();

	/**
	 * Constructor.
	 *
	 * @param string $namespace_prefix The namespace prefix for autoloading.
	 * @param string $base_dir         The base directory for autoloading.
	 */
	public function __construct( string $namespace_prefix, string $base_dir ) {
		$this->namespace_prefix = $namespace_prefix;
		$this->base_dir         = $base_dir;

		$this->add_psr4_mapping( $namespace_prefix, $base_dir . 'includes/' );
	}

	/**
	 * Register the autoloader.
	 *
	 * @return void
	 */
	public function register(): void {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Add a PSR-4 mapping.
	 *
	 * @param string $prefix  The namespace prefix.
	 * @param string $base_dir The base directory for the namespace prefix.
	 * @return void
	 */
	public function add_psr4_mapping( string $prefix, string $base_dir ): void {
		$prefix   = trim( $prefix, '\\' ) . '\\';
		$base_dir = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . '/';

		$this->psr4_mappings[ $prefix ] = $base_dir;
	}

	/**
	 * Autoload function for class loading.
	 *
	 * @param string $class Full class name.
	 * @return void
	 */
	public function autoload( string $class ): void {
		$prefix = $this->namespace_prefix . '\\';

		if ( strpos( $class, $prefix ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, strlen( $prefix ) );
		$mapped_file    = $this->load_mapped_file( $relative_class );

		if ( $mapped_file ) {
			require_once $mapped_file;
		}
	}

	/**
	 * Load the mapped file for a namespace prefix and relative class.
	 *
	 * @param string $relative_class The relative class name.
	 * @return string|false The mapped file name on success, or false on failure.
	 */
	private function load_mapped_file( string $relative_class ) {
		foreach ( $this->psr4_mappings as $prefix => $dir ) {
			if ( strpos( $relative_class, $prefix ) === 0 ) {
				$file_name = 'class-' . str_replace( '_', '-', strtolower( substr( $relative_class, strlen( $prefix ) ) ) ) . '.php';
				$file      = $dir . str_replace( '\\', '/', $file_name );

				if ( $this->require_file( $file ) ) {
					return $file;
				}
			}
		}

		// Try the default mapping
		$file_name = 'class-' . str_replace( '_', '-', strtolower( $relative_class ) ) . '.php';
		$file      = $this->base_dir . 'includes/' . str_replace( '\\', '/', $file_name );
		return $this->require_file( $file ) ? $file : false;
	}

	/**
	 * If a file exists, require it from the file system.
	 *
	 * @param string $file The file to require.
	 * @return bool True if the file exists, false if not.
	 */
	private function require_file( string $file ): bool {
		if ( file_exists( $file ) ) {
			require_once $file;
			return true;
		}
		return false;
	}
}
