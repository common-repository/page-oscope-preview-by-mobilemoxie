<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/admin
 * @author     Your Name <email@example.com>
 */
class Mobilemoxie_page_oscope_preview_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $mobilemoxie_page_oscope_preview    The ID of this plugin.
	 */
	private $mobilemoxie_page_oscope_preview;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $mobilemoxie_page_oscope_preview       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $mobilemoxie_page_oscope_preview, $version ) {

		$this->mobilemoxie_page_oscope_preview = $mobilemoxie_page_oscope_preview;
		$this->version = $version;
	}

    /**
     * Add a menu item called 'Page-oscope Preview' to the left-hand admin menu bar
     */
    public function mobilemoxie_page_oscope_preview_admin_menu_bar_item()
    {
        add_menu_page( 'Page-oscope Preview', 'Page-oscope Preview', 'manage_options', 'mobilemoxie_page_oscope_preview', array( $this, 'mobilemoxie_page_oscope_preview_admin_page' ), plugins_url('/img/mobilemoxie-icon.png', __FILE__), 21 );
    }

    /**
     * Add details to the publish right-hand meta box area
     */
    public function mobilemoxie_page_oscope_preview_edit_page_meta_box_item()
    {
        add_meta_box('mobilemoxie_page_oscope_preview_meta', 'MobileMoxie Page-oscope Preview', array( $this, 'mobilemoxie_page_oscope_preview_meta_box' ), 'page', 'side');
    }

    private function  mobilemoxie_page_oscope_preview_post_is_gutenberg($post)
    {
        $current_screen = get_current_screen();
        return method_exists( $current_screen, 'is_block_editor' ) && $current_screen->is_block_editor();
    }

    /**
     * Creates a random, one time use token. Without an UID.
     *
     * @see wp_create_nonce()
     *
     * @since 1.0.0
     *
     * @param  string|int $action Scalar value to add context to the nonce.
     * @return string The one use form token.
     */
    private static function create_nonce( $action = -1 ) {
        $i = self::nonce_tick();

        return substr( wp_hash( $i . $action, 'nonce' ), -12, 10 );
    }

    /**
     * Get the time-dependent variable for nonce creation.
     *
     * @see wp_nonce_tick()
     *
     * @since 2.1.0
     *
     * @return int The time-dependent variable.
     */
    private static function nonce_tick() {
        $nonce_life = apply_filters( 'mmpop_nonce_life', 2 * DAY_IN_SECONDS ); // 2 days.

        return ceil( time() / ( $nonce_life / 2 ) );
    }

    /**
     * Returns the public preview link.
     *
     * The link is the home link with these parameters:
     *  - preview, always true (query var for core)
     *  - _mmpop, a custom nonce, see DS_Public_Post_Preview::create_nonce()
     *  - page_id or p or p and post_type to specify the post.
     *
     * @since 2.0.0
     *
     * @param WP_Post $post The post object.
     * @return string The generated public preview link.
     */
    public static function get_preview_link( $post ) {
        if ( 'page' === $post->post_type ) {
            $args = array(
                'page_id' => $post->ID,
            );
        } elseif ( 'post' === $post->post_type ) {
            $args = array(
                'p' => $post->ID,
            );
        } else {
            $args = array(
                'p'         => $post->ID,
                'post_type' => $post->post_type,
            );
        }

        $args['preview'] = true;
        $args['_mmpop']    = self::create_nonce( 'mobilemoxie_page_oscope_preview_' . $post->ID );

        $link = add_query_arg( $args, home_url( '/' ) );

        return apply_filters( 'mmpop_preview_link', $link, $post->ID, $post );
    }

    /**
     * Add details to the publish meta box
     */
    public function mobilemoxie_page_oscope_preview_post_submitbox_misc_actions()
    {
        $post = get_post();
        // only display here if gutenberg is not enabled for this page/post
        if ( ! $this-> mobilemoxie_page_oscope_preview_post_is_gutenberg($post) ) {
            // the plugin only works for published pages for now
            $isPublished = $post->post_status === 'publish';

            $mobilemoxieExternalToolPageoscopeBaseUrl = 'https://mobilemoxie.com/tools/mobile-page-test-wordpress-plugin/?';
            $pageoscopeIconUrl = plugin_dir_url(__FILE__) . 'img/mobilemoxie-page-oscope-icon.png';
            $pluginSettingsUrl = admin_url('admin.php?page=mobilemoxie_page_oscope_preview');

            if( !$isPublished )
            {
                $pageUrl = self::get_preview_link( $post );
            }
            else
            {
                $pageUrl = get_permalink();
            }
            $pageoscope_params = 'data_url=' . urlencode($pageUrl);
            $mobilemoxieExternalToolPageoscopeUrl = $mobilemoxieExternalToolPageoscopeBaseUrl . $pageoscope_params;

            require_once plugin_dir_path(dirname(__FILE__)) . 'admin/partials/mobilemoxie-page-oscope-preview-admin-publish-meta-box.php';
        }
    }

    /**
     * Display setup page
     */
    function mobilemoxie_page_oscope_preview_admin_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $screenshot1Url = plugin_dir_url( dirname( __FILE__ ) ) . 'assets/screenshot-1.jpg';
        // display signup form
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/mobilemoxie-page-oscope-preview-admin-form-signup.php';

    }

    /**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->mobilemoxie_page_oscope_preview, plugin_dir_url( __FILE__ ) . 'css/mobilemoxie-page-oscope-preview-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->mobilemoxie_page_oscope_preview . '-google-font', "https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0", array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->mobilemoxie_page_oscope_preview, plugin_dir_url( __FILE__ ) . 'js/mobilemoxie-page-oscope-preview-admin.js', array( 'jquery' ), $this->version, false );

        $post = get_post();
        if( $post ) {
            $isPublished = $post->post_status === 'publish';
            $isGutenberg = $this->mobilemoxie_page_oscope_preview_post_is_gutenberg($post) ? '1' : '0';

            $mobilemoxieExternalToolPageoscopeBaseUrl = 'https://mobilemoxie.com/tools/mobile-page-test-wordpress-plugin/?';
            $pageoscopeIconUrl = plugin_dir_url(__FILE__) . 'img/mobilemoxie-page-oscope-icon.png';
            $pluginSettingsUrl = admin_url('admin.php?page=mobilemoxie_page_oscope_preview');

            if( !$isPublished )
            {
                $pageUrl = self::get_preview_link( $post );
            }
            else
            {
                $pageUrl = get_permalink();
            }
            $pageoscope_params = 'data_url=' . urlencode($pageUrl);
            $mobilemoxieExternalToolPageoscopeUrl = $mobilemoxieExternalToolPageoscopeBaseUrl . $pageoscope_params;

            $data = array(
                'mobilemoxieExternalToolPageoscopeUrl' => $mobilemoxieExternalToolPageoscopeUrl,
                'pageoscopeIconUrl' => $pageoscopeIconUrl,
                'pluginSettingsUrl' => $pluginSettingsUrl,
                'isPublished' => $isPublished,
                'isGutenberg' => $isGutenberg
            );
            wp_localize_script($this->mobilemoxie_page_oscope_preview, 'mobilemoxie_page_oscope_preview_script_vars', $data);
        }
	}

}
