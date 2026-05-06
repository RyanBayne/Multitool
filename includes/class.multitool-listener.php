<?php
/**
 * Multitool listener class for loading objects only when
 * the request requires it.
 * 
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_Listener' ) ) :

/**
 * Multitool_Listener.
 */
class Multitool_Listener {
       
    /**
    * Mainly for hooks. 
    */
    public static function init() {   
        // Do nothing if main listener switch is not set to 'yes'.   
        if( get_option( 'multitool_main_listener_switch' ) != 'yes' )  {
            return;
        }          
        
        add_action( 'init', array( __CLASS__, 'public_listener' ) );     
        add_action( 'admin_init', array( __CLASS__, 'admin_listener' ) );     
    }
    
    /**
    * Use settings or algorithms to determine what tools to run. 
    *
    * @version 1.0
    */
    public static function admin_listener() { 
        // Create administration only objects.    
        if( !multitool_is_request( 'admin' ) ) { return; }
        if( get_option( 'multitool_main_listener_switch' ) != 'yes' ) { return; } 
        if( get_option( 'multitool_admin_listener_switch' ) != 'yes' ) { return; } 
    }
       
    /**
    * Use settings or algorithms to determine what tools to run. 
    *
    * @version 1.0
    */
    public static function public_listener() { 
        if( get_option( 'multitool_main_listener_switch' ) != 'yes' ) { return; } 
        if( get_option( 'multitool_public_listener_switch' ) != 'yes' ) { return; } 

        // Display Errors Tool            
        if( get_option( 'multitool_displayerrors' ) == 'yes' ) {            
            $multitool_debug = new Multitool_Debug();
            $multitool_debug->debugmode();
            unset( $multitool_debug );
        }
        
        // Maintenance Mode Tool
        if( get_option( 'multitool_maintenancemode_activate' ) == 'yes' ) {
            // Some tools require their own class, they just keep growing!
            // Including this file is all we need to do to run the service/tool.
            include_once( '/tools/class.multitool-maintenance-tool.php' );
        }
    }
}

endif;

$MultitoolListener = new Multitool_Listener();
$MultitoolListener->init();
unset($MultitoolListener);