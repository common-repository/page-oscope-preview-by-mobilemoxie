<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 *
 * @package    Mobilemoxie_page_oscope_preview
 * @subpackage Mobilemoxie_page_oscope_preview/public
 * @author     Your Name <email@example.com>
 */
class Mobilemoxie_page_oscope_preview_Public {

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
	 * @param      string    $mobilemoxie_page_oscope_preview       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $mobilemoxie_page_oscope_preview, $version ) {

		$this->mobilemoxie_page_oscope_preview = $mobilemoxie_page_oscope_preview;
		$this->version = $version;

	}

    /**
     * Registers the new query var `_mmpop`.
     *
     * @since 1.0.0
     *
     * @param  array $qv Existing list of query variables.
     * @return array List of query variables.
     */
    public static function add_query_var( $qv ) {
        $qv[] = '_mmpop';

        return $qv;
    }

    /**
     * Registers the filter to handle a public preview.
     *
     * Filter will be set if it's the main query, a preview, a singular page
     * and the query var `_mmpop` exists.
     *
     * @since 1.0.0
     *
     * @param object $query The WP_Query object.
     */
    public static function show_public_preview( $query ) {
        if (
            $query->is_main_query() &&
            $query->is_preview() &&
            $query->is_singular() &&
            $query->get( '_mmpop' )
        ) {
            if ( ! headers_sent() ) {
                nocache_headers();
                header( 'X-Robots-Tag: noindex' );
            }
            if ( function_exists( 'wp_robots_no_robots' ) ) { // WordPress 5.7+
                add_filter( 'wp_robots', 'wp_robots_no_robots' );
            } else {
                add_action( 'wp_head', 'wp_no_robots' );
            }

            add_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10, 2 );
        }
    }

    /**
     * Sets the post status of the first post to publish, so we don't have to do anything
     * *too* hacky to get it to load the preview.
     *
     * @since 1.0.0
     *
     * @param  array $posts The post to preview.
     * @return array The post that is being previewed.
     */
    public static function set_post_to_publish( $posts ) {
        // Remove the filter again, otherwise it will be applied to other queries too.
        remove_filter( 'posts_results', array( __CLASS__, 'set_post_to_publish' ), 10 );

        if ( empty( $posts ) ) {
            return $posts;
        }

        $post_id = (int) $posts[0]->ID;

        // If the post has gone live, redirect to it's proper permalink.
        self::maybe_redirect_to_published_post( $post_id );

        if ( self::is_public_preview_available( $post_id ) ) {
            // Set post status to publish so that it's visible.
            $posts[0]->post_status = 'publish';

            // Disable comments and pings for this post.
            add_filter( 'comments_open', '__return_false' );
            add_filter( 'pings_open', '__return_false' );
            add_filter( 'wp_link_pages_link', array( __CLASS__, 'filter_wp_link_pages_link' ), 10, 2 );
        }

        return $posts;
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
     * Verifies that correct nonce was used with time limit. Without an UID.
     * @see wp_verify_nonce()
     *
     * @since 1.0.0
     *
     * @param string     $nonce  Nonce that was used in the form to verify.
     * @param string|int $action Should give context to what is taking place and be the same when nonce was created.
     * @return bool               Whether the nonce check passed or failed.
     */
    private static function verify_nonce( $nonce, $action = -1 ) {
        $i = self::nonce_tick();

        // Nonce generated 0-12 hours ago.
        if ( substr( wp_hash( $i . $action, 'nonce' ), -12, 10 ) === $nonce ) {
            return 1;
        }

        // Nonce generated 12-24 hours ago.
        if ( substr( wp_hash( ( $i - 1 ) . $action, 'nonce' ), -12, 10 ) === $nonce ) {
            return 2;
        }

        // Invalid nonce.
        return false;
    }

    /**
     * Checks if a public preview is available
     *
     * @since 1.0.0
     *
     * @param int $post_id The post id.
     * @return bool True if a public preview is allowed, false on a failure.
     */
    private static function is_public_preview_available( $post_id ) {
        if ( empty( $post_id ) ) {
            return false;
        }

        if ( ! self::verify_nonce( get_query_var( '_mmpop' ), 'mobilemoxie_page_oscope_preview_' . $post_id ) ) {
            wp_die( __( 'This link has expired!', 'mobilemoxie-page-oscope-preview' ), 403 );
        }

        return true;
    }

    /**
     * Redirects to post's proper permalink, if it has gone live.
     *
     * @since 1.0.0
     *
     * @param int $post_id The post id.
     * @return false False of post status is not a published status.
     */
    private static function maybe_redirect_to_published_post( $post_id ) {
        if ( ! in_array( get_post_status( $post_id ), self::get_published_statuses(), true ) ) {
            return false;
        }

        wp_safe_redirect( get_permalink( $post_id ), 301 );
        exit;
    }

    /**
     * Returns post statuses which represent a published post.
     *
     * @since 1.0.0
     *
     * @return array List with post statuses.
     */
    private static function get_published_statuses() {
        $published_statuses = array( 'publish', 'private' );

        return apply_filters( 'mmpop_published_statuses', $published_statuses );
    }
}
