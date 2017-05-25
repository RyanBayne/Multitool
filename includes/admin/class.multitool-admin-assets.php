<?php
/**
 * Multitool - Load Assets 
 *
 * Load admin only js, css, images and fonts. 
 *
 * @author   Ryan Bayne
 * @category Loading
 * @package  Multitool/Loading
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Multitool_Admin_Assets' ) ) :

/**
 * Multitool_Admin_Assets Class.
 */
class Multitool_Admin_Assets {

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) ); 
    }

    /**
     * Enqueue styles for the admin side.
     */
    public function admin_styles() {
        global $wp_scripts;
        
        // Screen ID Must be set for later arguments
        $screen         = get_current_screen();
        $screen_id      = $screen ? $screen->id : '';
        
        $jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.11.4';

        // Register admin styles
        wp_register_style( 'multitool_admin_styles', GlobalMultitool()->plugin_url() . '/assets/css/admin.css', array(), MULTITOOL_VERSION );
        wp_register_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.min.css', array(), $jquery_version );

        // Admin styles for WordPress Seed pages only
        /*
        if ( in_array( $screen_id, multitool_get_screen_ids() ) ) {
            wp_enqueue_style( 'multitool_admin_styles' );
            wp_enqueue_style( 'jquery-ui-style' );
        }
        */
    }

    /**
     * Enqueue scripts for the admin side.
     */
    public function admin_scripts() {                   
        global $wp_query, $post;

        $screen       = get_current_screen();
        $screen_id    = $screen ? $screen->id : '';
        $package_screen_id = sanitize_title( __( 'Multitool', 'multitool' ) );
        $suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        // Register scripts
        wp_register_script( 'multitool_admin', GlobalMultitool()->plugin_url() . '/assets/js/admin/multitool_admin.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core' ), MULTITOOL_VERSION );

        if ( in_array( $screen_id, multitool_get_screen_ids() ) ) {         
            wp_enqueue_script( 'multitool_admin' );
        } 
                                   
    }
}

endif;

return new Multitool_Admin_Assets();
