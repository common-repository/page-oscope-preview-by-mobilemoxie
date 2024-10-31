<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/includes
 * @author     Your Name <email@example.com>
 */
class Mobilemoxie_page_oscope_preview_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mobilemoxie-page-oscope-preview',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
