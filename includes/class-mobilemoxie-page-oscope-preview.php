<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/includes
 * @author     Your Name <email@example.com>
 */
class Mobilemoxie_page_oscope_preview {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Mobilemoxie_page_oscope_preview_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $mobilemoxie_page_oscope_preview    The string used to uniquely identify this plugin.
	 */
	protected $mobilemoxie_page_oscope_preview;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'Mobilemoxie_page_oscope_preview_VERSION' ) ) {
			$this->version = Mobilemoxie_page_oscope_preview_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->mobilemoxie_page_oscope_preview = 'mobilemoxie-page-oscope-preview';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Mobilemoxie_page_oscope_preview_Loader. Orchestrates the hooks of the plugin.
	 * - Mobilemoxie_page_oscope_preview_i18n. Defines internationalization functionality.
	 * - Mobilemoxie_page_oscope_preview_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilemoxie-page-oscope-preview-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-mobilemoxie-page-oscope-preview-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-mobilemoxie-page-oscope-preview-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-mobilemoxie-page-oscope-preview-public.php';

		$this->loader = new Mobilemoxie_page_oscope_preview_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Mobilemoxie_page_oscope_preview_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Mobilemoxie_page_oscope_preview_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Mobilemoxie_page_oscope_preview_Admin( $this->get_mobilemoxie_page_oscope_preview(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        // admin left hand menu item
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'mobilemoxie_page_oscope_preview_admin_menu_bar_item' );

        // add details to publish meta box
        $this->loader->add_action( 'post_submitbox_misc_actions', $plugin_admin, 'mobilemoxie_page_oscope_preview_post_submitbox_misc_actions' );

	}


    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Mobilemoxie_page_oscope_preview_Public( $this->get_mobilemoxie_page_oscope_preview(), $this->get_version() );

        $this->loader->add_action( 'pre_get_posts', $plugin_public, 'show_public_preview' );
        $this->loader->add_action( 'query_vars', $plugin_public, 'add_query_var' );
        // Add the query var to WordPress SEO by Yoast whitelist.
        $this->loader->add_action( 'wpseo_whitelist_permalink_vars', $plugin_public, 'add_query_var' );

    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_mobilemoxie_page_oscope_preview() {
		return $this->mobilemoxie_page_oscope_preview;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Mobilemoxie_page_oscope_preview_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
