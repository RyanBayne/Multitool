<?php
/**
 * Multitool Developer Tools
 * 
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_Plugin_Tools' ) ) :

/**
 * Multitool_Developer_Tools
 */
class Multitool_Developer_Tools extends Multitool_Settings_Page {

    // List of activation states for responding to changes. 
    public $switch_maintenancemode = null;
    
    /**
     * Constructor.
     */
    public function __construct() {

        $this->id    = 'developertools';
        $this->label = __( 'Developer Tools', 'multitool' );

        add_filter( 'multitool_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
        add_action( 'multitool_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'multitool_settings_save_' . $this->id, array( $this, 'save' ) );
        
        // Do custom notices for individual settings, usually the activation checkbox.
        register_setting('multitool_maintenancemode_activate', 'multitool_maintenancemode_activate', array( $this, 'maintenancemode_activatation_notice' ) );        
        
        // Update list of switches
        $this->switch_maintenancemode = get_option( 'multitool_maintenancemode_activate' );
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {

        $settings = apply_filters( 'multitool_developers_tools_settings', array(

            // Section Start: Maintenance Mode
            array( 'title' => __( 'Maintenance Mode', 'multitool' ), 
                   'type' => 'title', 
                   'desc' => __( 'Configure your maintenance notice. Activating this tool make a Quick Tool available so that you can switch maintenance mode on and off quickly.', 'multitool' ), 
                   'id' => 'multitool_configtool_maintenancemode' 
            ),
            
            array(
                'title'   => __( 'Activate Tool', 'multitool' ),
                'desc'    => __( 'Activates the Maintenance Mode tool and makes a Quick Tool available.', 'multitool' ),
                'id'      => 'multitool_maintenancemode_activate',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
            
            array(
                'title'    => __( 'Time Limit (Minutes)', 'multitool' ),
                'id'       => 'multitool_maintenancemode_timelimit',
                'css'      => 'width:50px;',
                'default'  => '1',
                'desc_tip' =>  true,
                'type'     => 'number',
                'custom_attributes' => array(
                    'min'  => 1,
                    'step' => 5
                )
            ),
                    
            array( 
                'type' => 'sectionend', 
                'id' => 'multitool_configtool_pluginsnapshot'),
            // Section End: Maintenance Mode
            
        ) );

        return apply_filters( 'multitool_get_settings_' . $this->id, $settings );
    }

    /**
     * Save settings.
     * 
     * @version 1.1
     */
    public function save() {
        $settings = $this->get_settings();
        
        // Include tool classes. 
        include_once( MULTITOOL_PLUGIN_DIR_PATH . 'includes/tools/class.multitool-maintenance-tool.php' );
        
        // Ensure potentially damaging tools are always admin only.
        if( current_user_can( 'activate_plugins' ) ) {        
            // Activate maintenance mode switch.        
            if( isset( $_POST['multitool_maintenancemode_activate'] ) && $this->switch_maintenancemode != 'yes' ) {
                
                // Run activation in maintenance mode class. 
                Multitool_Maintenance_Mode_Tool::activate();
                
            } elseif( !isset( $_POST['multitool_maintenancemode_activate'] ) && $this->switch_maintenancemode == 'yes' ) {
                
                // Run deactivation in maintenance mode class if maintenance mode is active. 
                Multitool_Maintenance_Mode_Tool::disable();

            } 
        }// End of administration check.
                
        Multitool_Admin_Settings::save_fields( $settings );
    }

    /**
    * Maintenance Mode Activation Notice
    * 
    * @param mixed $input
    * 
    * @version 1.0
    */
    public function maintenancemode_activatation_notice( $input ) {     

        // Activate maintenance mode switch.        
        if( isset( $_POST['multitool_maintenancemode_activate'] ) && $this->switch_maintenancemode != 'yes' ) {
            
            Multitool_Admin_Notices::notice( 'success', __( 'Maintenance Mode Activated', 'multitool' ), __( 'Maintenance mode has been activated. Your visitors will see your under construction message until maintenance mode is turned off.', 'multitool' ) );
            
        } elseif( !isset( $_POST['multitool_maintenancemode_activate'] ) && $this->switch_maintenancemode == 'yes' ) {
            
            Multitool_Admin_Notices::notice( 'info', __( 'Maintenance Mode Disabled', 'multitool' ), __( 'Maintenance mode has been turned off. Your visitors will see your site normally.', 'multitool' ) );

        }
        
        // Avoid repeating the notice (the register_setting() hook runs twice for some reason)
        unregister_setting( 'multitool_maintenancemode_activate', 'multitool_maintenancemode_activate' );
                                 
        return $input;
    }
}

endif;

return new Multitool_Developer_Tools();
