<?php
/**
 * Plugin Name: MyPlugin
 * Description: This plugin does something.
 * Author: John Doe
 * Author URI: https://johndoe.com/
 * 
 * Version: 1.0
 * Requires PHP: 7.2
 * Requires at least: 5.9
 * Tested up to: 5.9
 * WC requires at least: 6.2
 * WC tested up to: 6.4
 * 
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * Text Domain: my-plugin
 * Domain Path: /languages
 * 
 * @package My Plugin
 * @author  John Doe
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define WDS_IM_PLUGIN_FILE.
define( 'MP_PLUGIN_FILE', __FILE__ );

// Check if another plugin is overwriting our class.
if ( class_exists( 'My_Plugin_Setup' ) ) {
    return;
}

// Include the main My Plugin_Setup class.
require_once dirname( __FILE__ ) . '/includes/class-my-plugin-setup.php';

/**
 * Main instance of My_Plugin.
 * 
 * @since  1.0
 * @return My_Plugin_Setup
 */
function my_plugin_setup() {
    return My_Plugin_Setup::instance();
}

// Setup the plugin.
my_plugin_setup();
