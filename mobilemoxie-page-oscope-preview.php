<?php /** @noinspection ALL */

/**
 *
 * @link              http://mobilemoxie.com
 * @since             1.0.0
 * @package           Mobilemoxie_page_oscope_preview
 *
 * @wordpress-plugin
 * Plugin Name:       Page-oscope Preview by MobileMoxie
 * Plugin URI:        https://mobilemoxie.com/
 * Description:       Preview your page on different devices to help you understand how they are working for mobile and desktop users as well as mobile search engine bots.
 * Version:           0.3
 * Author:            MobileMoxie
 * Author URI:        http://mobilemoxie.com/
 * Text Domain:       mobilemoxie-page-oscope-preview
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'Mobilemoxie_page_oscope_preview_VERSION', '0.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-mobilemoxie-page-oscope-preview-activator.php
 */
function activate_mobilemoxie_page_oscope_preview() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilemoxie-page-oscope-preview-activator.php';
	Mobilemoxie_page_oscope_preview_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-mobilemoxie-page-oscope-preview-deactivator.php
 */
function deactivate_mobilemoxie_page_oscope_preview() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-mobilemoxie-page-oscope-preview-deactivator.php';
	Mobilemoxie_page_oscope_preview_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_mobilemoxie_page_oscope_preview' );
register_deactivation_hook( __FILE__, 'deactivate_mobilemoxie_page_oscope_preview' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mobilemoxie-page-oscope-preview.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mobilemoxie_page_oscope_preview() {

	$plugin = new Mobilemoxie_page_oscope_preview();
	$plugin->run();

}
run_mobilemoxie_page_oscope_preview();
