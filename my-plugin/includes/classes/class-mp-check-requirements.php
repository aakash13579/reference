<?php
/**
 * My Plugin Check Requirements class
 * 
 * Checks if the plugin can run according to its requirements.
 * 
 * @package My Plugin/Classes
 * @version 1.0
 */

defined( 'ABSPATH' ) || exit;

final class Mp_Check_Requirements {

    /**
     * The single instance of the class.
     * 
     * @since 1.0
     */
    protected static $instance = NULL;

    /**
	 * Ensures only one instance of the class is loaded or can be loaded.
	 * 
	 * @since  1.0
	 * @static
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

    /**
     * Class Constructor.
     * 
     * @since 1.0
     */
    public function __construct() {

        add_action( 'admin_init', array( $this, 'check_version' ) );
		
    }

    /**
	 * Stop the plugin if the requirements are not met.
	 *
	 * @since 1.0
	 */
	public function stop_plugin() {

		if ( ! self::compatible_version() ) {
			return true;
		}

		if ( ! self::woocommerce_check() ) {
			return true;
		}

		return false;

	}

    /**
     * Check for compatible WordPress version.
     * 
     * @since 1.0
     * @static
     */
    public static function activation_check() {

        if ( ! self::compatible_version() ) {
            deactivate_plugins( plugin_basename( MP_PLUGIN_FILE ) );
            wp_die( sprintf( __( 'My Plugin requires WordPress %s or later.', 'my-plugin' ), MP_WP_VERSION ) );
        }

    }

    /**
	 * Check for comaptible WordPress version.
	 *
	 * @since 1.0
	 * @static
	 */
	// TODO also add check for WooCommerce Darabase version.
	public static function compatible_version() {

		if ( version_compare( $GLOBALS['wp_version'], MP_WP_VERSION, '<' ) ) {
			return false;
		}

		return true;

	}

    /**
	 * Check if WooCommerce is active and compatible.
	 *
	 * @since 1.0
	 * @static
	 */
    public static function woocommerce_check( bool $check_version = true ) {

        if ( ! in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins', array() ) ) || is_multisite() && ! array_key_exists( 'woocommerce/woocommerce.php', get_site_option( 'active_sitewide_plugins', array() ) ) ) {
            return false;
        }

		if ( $check_version ) {
			return self::woocommerce_check_version_only();
		}

        return true;

    }

    /**
	 * Check if WooCommerce is compatible.
	 *
	 * @since 1.0
	 * @static
	 */
    public static function woocommerce_check_version_only() {

        if( version_compare( get_option( 'woocommerce_version' ), MP_WC_VERSION, '<' ) ) {
            return false;
        }

        return true;
    }

    /**
	 * Check for compatible WordPress and WooCommerce version.
	 *
	 * @since 1.0
	 */
    public function check_version() {

		$deactivate = false;
        if ( ! self::compatible_version() ) {
			add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
			$deactivate = true;
		} elseif ( ! self::woocommerce_check() ) {
			add_action( 'admin_notices', array( $this, 'disabled_notice_woocommerce_check' ) );
			$deactivate = true;
		}

		if( ! $deactivate && ! is_plugin_active( MP_BASENAME ) ) {
			deactivate_plugins( MP_BASENAME );
		}
    }

	/**
	 * Add notice for WordPress version.
	 *
	 * @since 1.0
	 */
	public function disabled_notice() {

		echo '<div class="error fade"><p>';
		echo sprintf( __( '%sImportant:%s My Plugin requires WordPress %s or later.', 'my-plugin' ),
			'<strong>', '</strong>', MP_WP_VERSION );
		echo "</p></div>\n";

	}

    /**
	 * Add notice for WooCommerce version.
	 *
	 * @since 1.0
	 */
	public function disabled_notice_woocommerce_check() {

		echo '<div class="woocommerce-message error fade"><p>';
		if ( ! self::woocommerce_check(false) ) {
			echo sprintf( __( '%sImportant:%s My Plugin requires %sWooCommerce%s %s or later.', 'my-plugin' ),
				'<strong>', '</strong>', '<a href="http://wordpress.org/plugins/woocommerce/">', '</a>', MP_WC_VERSION );
		} else {
			echo sprintf( __( '%sImportant:%s My Plugin requires Woocommerce %s or later.',
            'my-plugin' ),
				'<strong>', '</strong>', MP_WC_VERSION );
		}
		echo '</p></div>';

	}

}