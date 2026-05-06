<?php
/**
 * Multitool Security Tools
 *
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_Security_Tools' ) ) :

/**
 * Multitool_Security_Tools.
 */
class Multitool_Security_Tools extends Multitool_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {

        $this->id    = 'securitytools';
        $this->label = __( 'Security Tools', 'multitool' );

        add_filter( 'multitool_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
        add_action( 'multitool_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'multitool_settings_save_' . $this->id, array( $this, 'save' ) );
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings() {

        $settings = apply_filters( 'multitool_security_tools_settings', array(

            array( 'title' => __( 'Administrator Accounts Cap', 'multitool' ), 
                   'type' => 'title', 
                   'desc' => __( 'This tool is not to be treated as a full security solution. It is an emergency measure for a very specific hacker activity. This tool works by changing extra administrator accounts to subscribers i.e. when a hacker injects them.', 'multitool' ), 
                   'id' => 'multitool_configtool_adminaccountscap' 
            ),
            
            array(
                'title'   => __( 'Activate Tool', 'woocommerce' ),
                'desc'    => __( 'Activates the Administrator Accounts Cap tool.', 'multitool' ),
                'id'      => 'adminaccountscap_activate',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
            
            array(
                'title'    => __( 'Alert Type', 'multitool' ),
                'id'       => 'adminaccountscap_alerttype',
                'desc_tip' =>  __( 'Choose how you want to be alerted to new administration accounts being detected.', 'multitool' ),
                'default'  => 'adminnotice',
                'type'     => 'select',
                'class'    => 'multitool-enhanced-select',
                'options'  => array(
                    ''          => __( 'Please select an item...', 'multitool' ),
                    'adminnotice'   => __( 'Admin Notice', 'multitool' ),
                    'email'   => __( 'Email', 'multitool' ),
                    'adminnoticeandemail' => __( 'Admin Notice and Email', 'multitool' ),
                ),
            ),
            
            array(
                'title'    => __( 'Admin Accounts Cap', 'multitool' ),
                'desc'     => __( 'Enter the maximum number of administration accounts permitted in your blog.', 'multitool' ),
                'id'       => 'adminaccountscap_limit',
                'css'      => 'width:50px;',
                'default'  => '1',
                'desc_tip' =>  true,
                'type'     => 'number',
                'custom_attributes' => array(
                    'min'  => 1,
                    'step' => 1
                )
            ),
                                    
            array( 
                'type' => 'sectionend', 
                'id' => 'multitool_configtool_adminaccountscap'),
            
        ) );

        return apply_filters( 'multitool_get_settings_' . $this->id, $settings );
    }

    /**
     * Save settings.
     */
    public function save() {
        $settings = $this->get_settings();
        Multitool_Admin_Settings::save_fields( $settings );
    }

}

endif;

return new Multitool_Security_Tools();
