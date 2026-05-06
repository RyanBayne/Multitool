<?php
/**
 * Multitool Main Admin Views
 *
 * @author      Multitool
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
               
if ( ! class_exists( 'Multitool_Admin_Main_Views' ) ) :
            
/**
 * Multitool_Admin_Main_Views Class.
 */
class Multitool_Admin_Main_Views {

    /**
     * Handles output of the main tables page in admin.
     */
    public static function output() {       
        $tabs              = self::get_tabs();
        $first_tab         = array_keys( $tabs );
        $current_tab       = ! empty( $_GET['tab'] ) ? sanitize_title( $_GET['tab'] ) : $first_tab[0];
        $current_tablelist = isset( $_GET['multitoolview'] ) ? sanitize_title( $_GET['multitoolview'] ) : current( array_keys( $tabs[ $current_tab ]['maintabviews'] ) );

        require_once( 'views/html-admin-page.php' );
    }

    /**
     * Returns the definitions for custom views within the main table view.
     *
     * @return array
     */
    public static function get_tabs() {
        $tabviews = array();
        
        // Basic List Tables
        $tabviews['basic_list_tables'] = array(
            'title'  => __( 'Basic List Tables', 'multitool' ),
            'maintabviews' => array(
                "quicktools_all" => array(
                    'title'       => __( 'All Tools', 'multitool' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( __CLASS__, 'get_maintabview' )
                ),                    
                "quicktools_used" => array(
                    'title'       => __( 'Used Tools', 'multitool' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( __CLASS__, 'get_maintabview' )
                ),
                "quicktools_updated" => array(
                    'title'       => __( 'Updated Tools', 'multitool' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( __CLASS__, 'get_maintabview' )
                ),
                "quicktools_new" => array(
                    'title'       => __( 'New Tools', 'multitool' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( __CLASS__, 'get_maintabview' )
                ),
            )
        );
 
        $tabviews = apply_filters( 'multitool_admin_mainviews', $tabviews );

        return $tabviews;
    }

    /**
     * Get a report from our table view from the subfolder.
     */
    public static function get_maintabview( $name_presan ) {     
        $name  = sanitize_title( str_replace( '_', '-', $name_presan ) );
        $class = 'Multitool_' . str_replace( '-', '_', $name );
        
        require_once( apply_filters( 'multitool_admin_mainviews_path', 'mainviews/class.multitool-' . $name . '.php', $name, $class ) );

        if ( ! class_exists( $class ) )
            return;

        $maintabs = new $class();      
        $maintabs->output_result();
    }
}

endif;
