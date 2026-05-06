<?php
/**
 * Multitool - Plugin Menus
 *
 * Maintain plugins admin menu and tab-menus here.  
 *
 * @author   Ryan Bayne
 * @category User Interface
 * @package  Multitool/Admin
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Multitool_Admin_Menus' ) ) :

/**
 * Multitool_Admin_Menus Class.
 */
class Multitool_Admin_Menus {

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'menu_quicktools' ), 100 );
        add_action( 'admin_menu', array( $this, 'menu_configtools' ), 100 );
        //add_action( 'admin_menu', array( $this, 'menu_advancedtools' ), 100 );
    }

    public function menu_quicktools() {
        add_management_page( __( 'Quick Tools', 'multitool' ), __( 'Quick Tools', 'multitool' ), 'activate_plugins', 'multitool-quick', array( $this, 'page_quicktools' ) );
    }

    public function menu_configtools() {
        add_management_page( __( 'Config Tools', 'multitool' ), __( 'Config Tools', 'multitool' ), 'activate_plugins', 'multitool-config', array( $this, 'page_configtools' ) );        
    } 

    public function menu_advancedtools() {
        add_management_page( __( 'Advanced Tools', 'multitool' ), __( 'Advanced Tools', 'multitool' ), 'activate_plugins', 'multitool-advanced', array( $this, 'page_advancedtools' ) );        
    } 
        
    public function page_quicktools() { 
        Multitool_Admin_Main_Views::output(); 
    }

    public function page_configtools() {    
        Multitool_Admin_Settings::output();
    }

    public function page_advancedtools() {    
    
    } 
}

endif;

return new Multitool_Admin_Menus();