<?php
/**
 * Multitool Maintenance Mode Tool Class
 * 
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_CLASSNAME' ) ) :

/**
 * Multitool_Maintenance_Mode_Tool.
 */
class Multitool_Maintenance_Mode_Tool {
    public static function init() {
        if( get_option( 'multitool_maintenancemode_activate' ) != 'yes' ) { return; }
        
        // Add actions and filters.
        add_action('template_include', array( __CLASS__, 'template'), 9999);
        
        // Check for possible expirty of under construction period.
        self::expire();
    }
    
    public static function template() {
        echo __( 'This site is currently under maintenance. Please return tomorrow.', 'multitool' );
    }
    
    /**
    * Activate maintenance mode. 
    * 
    * @version 1.0
    */
    public static function activate( $duration = null ) {
        update_option( 'multitool_maintenancemode_activate', 'yes', true );
        update_option( 'multitool_maintenancemode_activation_time', time() );
        
        if( $duration ) {
            update_option( 'multitool_maintenancemode_timelimit', $duration );
        }    
    }
    
    /**
    * Disable maintenance mode and cleanup related options.
    * 
    * @version 1.0
    */
    public static function disable() {
        delete_option( 'multitool_maintenancemode_activate' );
        delete_option( 'multitool_maintenancemode_timelimit' );
        delete_option( 'multitool_maintenancemode_activation_time' );
    }
    
    /**
    * Determine how much time has passed since maintenance mode was
    * activated. If the set time has passed then maintenance mode is
    * disabled. 
    * 
    * @version 1.0           
    */
    public static function expire() {
        // Get users maximum under constructon period (in minutes)
        $stored_time_limit_minutes = get_option( 'multitool_maintenancemode_timelimit' );// Minutes
        
        // Convert our minutes to seconds for time() calculations.
        $stored_time_limit_seconds = 60 * $stored_time_limit_minutes;
    
        // If no expiry time set then return false, indicating no change.
        if( !$stored_time_limit_minutes ) { return false; }    
      
        $stored_activation_time = get_option( 'multitool_maintenancemode_activation_time' );
        
        $total = $stored_activation_time + $stored_time_limit_seconds;

        // If the current time is greater than $total disable maintenance mode.
        if( time() > $total ) {
            self::disable();        
        }
        
    }
   
}

endif;

Multitool_Maintenance_Mode_Tool::init();