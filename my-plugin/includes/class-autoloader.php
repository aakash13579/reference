<?php
/**
 * My Plugin Autoloader
 * 
 * Autoload classes on demand.
 * 
 * @package My Plugin/Classes
 * @since	1.0
 */

defined( 'ABSPATH' ) || exit;

class Mp_Autoloader {

	/**
	 * Class Constructor.
	 * 
	 * @since 1.0
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Take a class name and turns it into a file name.
	 * 
	 * @param  string $class Class name
	 * 
	 * @since  1.0
	 * @return string
	 */
	private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 * 
	 * @param  string $path Class file path.
	 * 
	 * @since  1.0
	 * @return bool
	 */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			include_once $path;

			return true;
		}

		return false;
	}

	/**
	 * Auto-load classes.
	 * 
	 * @since 1.0
	 * @param string $class Class name.
	 */
	public function autoload( $class ) {

		$path			= NULL;
		$original_class	= $class;
		$class			= strtolower( $class );
		$file			= $this->get_file_name_from_class( $class );

		if ( 0 === strpos( $class, 'mp_admin_' ) ) {
			$path = MP_PLUGIN_PATH . '/admin/classes/';
		} elseif ( 0 === strpos( $class, 'mp_public' ) ) {
			$path = MP_PLUGIN_PATH . '/public/classes/';
		} else {
			$path = MP_PLUGIN_PATH . '/includes/classes/';
		}

		if ( $path ) {
			$this->load_file( $path . $file );
		}

	}
	
}

new Mp_Autoloader();
