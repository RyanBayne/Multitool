<?php
/**
 * Multitool Ajax Event Handler.
 *                           
 * @package  Multitool/Core
 * @category Ajax
 * @author   Ryan Bayne
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Multitool_AJAX {

    /**
     * Hook in ajax handlers.
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
        add_action( 'template_redirect', array( __CLASS__, 'do_multitool_ajax' ), 0 );
        self::add_ajax_events();
    }

    /**
     * Get Multitool Ajax Endpoint.
     * @param  string $request Optional
     * @return string
     */
    public static function get_endpoint( $request = '' ) {
        return esc_url_raw( apply_filters( 'multitool_ajax_get_endpoint', add_query_arg( 'multitool-ajax', $request, remove_query_arg( array( 'remove_item', 'add-to-cart', 'added-to-cart' ) ) ), $request ) );
    }

    /**
     * Set Multitool AJAX constant and headers.
     */
    public static function define_ajax() {
        if ( ! empty( $_GET['multitool-ajax'] ) ) {
            if ( ! defined( 'DOING_AJAX' ) ) {
                define( 'DOING_AJAX', true );
            }
            if ( ! defined( 'MULTITOOL_DOING_AJAX' ) ) {
                define( 'MULTITOOL_DOING_AJAX', true );
            }
            // Turn off display_errors during AJAX events to prevent malformed JSON
            if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
                @ini_set( 'display_errors', 0 );
            }
            $GLOBALS['wpdb']->hide_errors();
        }
    }

    /**
     * Send headers for Multitool Ajax Requests
     */
    private static function multitool_ajax_headers() {
        send_origin_headers();
        @header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
        @header( 'X-Robots-Tag: noindex' );
        send_nosniff_header();
        nocache_headers();
        status_header( 200 );
    }

    /**
     * Check for Multitool Ajax request and fire action.
     */
    public static function do_multitool_ajax() {
        global $wp_query;

        if ( ! empty( $_GET['multitool-ajax'] ) ) {
            $wp_query->set( 'multitool-ajax', sanitize_text_field( $_GET['multitool-ajax'] ) );
        }

        if ( $action = $wp_query->get( 'multitool-ajax' ) ) {
            self::multitool_ajax_headers();
            do_action( 'multitool_ajax_' . sanitize_text_field( $action ) );
            die();
        }
    }

    /**
     * Hook in methods - uses WordPress ajax handlers (admin-ajax).
     */
    public static function add_ajax_events() {
        // multitool_EVENT => nopriv
        $ajax_events = array();

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_multitool_' . $ajax_event, array( __CLASS__, $ajax_event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_multitool_' . $ajax_event, array( __CLASS__, $ajax_event ) );

                // Multitool AJAX can be used for frontend ajax requests
                add_action( 'multitool_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
            }
        }
    }
}

Multitool_AJAX::init();
