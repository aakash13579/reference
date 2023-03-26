<?php
/**
 * My Plugin Setup
 * 
 * This class is responsible for setting up the plugin.
 * 
 * @package My Plugin
 * @since	1.0
 */

final class My_plugin_Setup {

	/**
	 * The single Instance of the class.
	 * 
	 * @since 1.0
	 */
	protected static $instance = null;

	/**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 * 
	 * @since  1.0
	 * @static
	 * @return My_plugin_Setup - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Cloning is forbidden.
	 * 
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cloning is forbidden.', 'my-plugin' ), '1.0' );
	}

	/**
	 * Unserializing is forbidden.
	 * 
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Unserializing is forbidden.', 'my-plugin' ), '1.0' );
	}

	/**
	 * Class constructor.
	 * 
	 * @since 1.0
	 */
	public function __construct() {
		if ( function_exists( 'wp_installing' ) && wp_installing() ) {
			return;
		}

		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'mp_loaded' );
	}

	/**
	 * Define constant if not already set.
	 * 
	 * @param string	  $name  Constant name.
	 * @param string|bool $value Constant value.
	 * 
	 * @since 1.0
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Define constants.
	 * 
	 * @since 1.0
	 */
	private function define_constants() {

		$version = get_file_data(
			MP_PLUGIN_FILE,
			array(
				'version'     => 'Version',
				'wp_required' => 'Requires at least',
				'wc_required' => 'WC requires at least'
			)
		);

		$this->define( 'MP_ABSPATH', dirname( MP_PLUGIN_FILE ) . '/' );
		$this->define( 'MP_VERSION', $version['version'] );
		$this->define( 'MP_WP_VERSION', $version['wp_required'] );
		$this->define( 'MP_WC_VERSION', $version['wc_required'] );
		$this->define( 'MP_PLUGIN_PATH', untrailingslashit( plugin_dir_path( MP_PLUGIN_FILE ) ) );
		$this->define( 'MP_PLUGIN_URL', untrailingslashit( plugins_url( '/', MP_PLUGIN_FILE ) ) );
		$this->define( 'MP_TEMPLATE_PATH', MP_PLUGIN_PATH . '/templates/' );
		$this->define( 'MP_DIRECTORY', dirname( plugin_basename( MP_PLUGIN_FILE ) ) );
		$this->define( 'MP_BASENAME', plugin_basename( MP_PLUGIN_FILE ) );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 * 
	 * @since 1.0
	 */
	public function includes() {

		// Class Autoloader
		include_once MP_PLUGIN_PATH . '/includes/class-autoloader.php';

		// Functions
		include_once MP_PLUGIN_PATH . '/includes/functions/functions.php';

	}

	/**
	 * Hook into actions and filters.
	 * 
	 * @since 1.0
	 */
	private function init_hooks() {

		// Check if the plugin can be activated.
		register_activation_hook( MP_PLUGIN_FILE, array( 'Mp_Check_Requirements', 'activation_check' ) );

		// Stop and exit plugin initiation.
		if ( mp_check_requirements()->stop_plugin() ) {
			return;
		}

		// Load plugin textdomain.
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 10 );

		// Register Some Taxonomy.
		mp_some_taxonomy();

		// TODO add the constant MP_POST_TYPES
		// add_action( 'init', array( 'MP_POST_TYPES', 'register' ), 10 ); 

		if ( is_admin() ) {
			// sets up admin.
			mp_admin_setup();
		}else{
			mp_public_setup();
		}

	/**
	 * Load plugin textdomain.
	 * 
	 * @since 1.0
	 */
	// TODO create some translate files
	public function load_textdomain() {

		$domain		= 'my-plugin';
		$locale		= apply_filters( 'plugin_locale', get_locale(), $domain );
		$global_mo  = trailingslashit( WP_LANG_DIR ) . 'plugins/' . $domain . '-' . $locale . '.mo';
		$global_mo2 = trailingslashit( WP_LANG_DIR ) .'plugins/' . $domain . '/' . $domain . '-' . $locale . '.mo';

		if ( file_exists( $global_mo ) ) {
			// wp-content/languages/plugins/plugin-name-$locale.mo
			load_textdomain( $domain, $global_mo );
		} elseif ( file_exists( $global_mo2 ) ) {
			// wp-content/languages/plugins/plugin-name/plugin-name-$locale.mo
			load_textdomain( $domain, $global_mo2 );
		} else {
			// wp-content/plugins/plugin-name/languages/plugin-name-$locale.mo
			load_plugin_textdomain( $domain, false, $domain . '/languages/' );
		}

	}

}