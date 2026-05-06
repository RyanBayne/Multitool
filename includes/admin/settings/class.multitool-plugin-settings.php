<?php
/**
 * Multitool Plugin Settings
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  Multitool/Admin
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_Plugin_Settings' ) ) :

/**
 * Multitool_Settings_Sections.
 */
class Multitool_Plugin_Settings extends Multitool_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {

        $this->id    = 'pluginsettings';
        $this->label = __( 'Multitool Settings', 'multitool' );

        add_filter( 'multitool_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
        add_action( 'multitool_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'multitool_settings_save_' . $this->id, array( $this, 'save' ) );
        add_action( 'multitool_sections_' . $this->id, array( $this, 'output_sections' ) );
    }

    /**
     * Get sections.
     *
     * @return array
     */
    public function get_sections() {

        $sections = array(
            ''              => __( 'Section A', 'multitool' ),
            //'sectionb'       => __( 'Section B', 'multitool' ),
        );

        return apply_filters( 'multitool_get_sections_' . $this->id, $sections );
    }

    /**
     * Output the settings.
     */
    public function output() {
        global $current_section;

        $settings = $this->get_settings( $current_section );

        Multitool_Admin_Settings::output_fields( $settings );
    }

    /**
     * Save settings.
     */
    public function save() {
        global $current_section;
        $settings = $this->get_settings( $current_section );
        Multitool_Admin_Settings::save_fields( $settings );
    }

    /**
     * Get settings array.
     *
     * @return array
     */
    public function get_settings( $current_section = '' ) {
        if ( 'sectionb' == $current_section ) {

            $settings = apply_filters( 'multitool_sectionb_settings', array(
            
                array(
                    'title' => __( 'Title and Introduction Example', 'multitool' ),
                    'type'     => 'title',
                    'desc'     => __( 'This is the example of an introduction which is part of the titles data.', 'multitool' ),
                    'id'     => 'image_options'
                ),

                array(
                    'title'         => __( 'Example Checkbox', 'multitool' ),
                    'desc'          => __( 'Example input descripton.', 'multitool' ),
                    'id'            => 'multitool_enable_examplecheckbox2',
                    'default'       => 'yes',
                    'desc_tip'      => __( 'This is an example of a tip.', 'multitool' ),
                    'type'          => 'checkbox'
                ),

                array(
                    'type'     => 'sectionend',
                    'id'     => 'image_options'
                )

            ));
        } else {
            $settings = apply_filters( 'multitool_general_settings', array(
 
                array(
                    'title' => __( 'Logging Settings', 'multitool' ),
                    'type'     => 'title',
                    'desc'     => '',
                    'id'     => 'multitool_logging_settings',
                ),

                array(
                    'title'           => __( 'Activate Logs', 'multitool' ),
                    'desc'            => __( 'Quick Tools Log', 'multitool' ),
                    'id'              => 'multitool_activate_quicktool_log',
                    'default'         => 'yes',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => 'start',
                    'show_if_checked' => 'option',
                ),

                array(
                    'desc'            => __( 'Configuration Tools Log', 'multitool' ),
                    'id'              => 'multitool_activate_configtool_log',
                    'default'         => 'yes',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => '',
                    'show_if_checked' => 'yes',
                    'autoload'        => false,
                ),

                array(
                    'desc'            => __( 'Advanced Tools Log', 'multitool' ),
                    'id'              => 'multitool_activate_advancedtool_log',
                    'default'         => 'no',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => 'end',
                    'show_if_checked' => 'yes',
                    'autoload'        => false,
                ),

                array(
                    'type'     => 'sectionend',
                    'id'     => 'multitool_logging_settings'
                ),

                array(
                    'title' => __( 'Listener Controls', 'multitool' ),
                    'type'     => 'title',
                    'desc'     => __( 'Listeners are parts of the plugin that decide what tools to run anytime a page is loaded. Switching listeners off will switch off any tools that depend on them. Those are tools that are automated.', 'multitool' ),
                    'id'     => 'multitool_listener_control_settings',
                ),

                array(
                    'title'           => __( 'Listener Switches', 'multitool' ),
                    'desc'            => __( 'Main Listener Switch', 'multitool' ),
                    'id'              => 'multitool_main_listener_switch',
                    'default'         => 'yes',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => 'start',
                    'show_if_checked' => 'option',
                    'autoload'        => true,
                ),

                array(
                    'desc'            => __( 'Public Listener Switch', 'multitool' ),
                    'id'              => 'multitool_public_listener_switch',
                    'default'         => 'yes',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => '',
                    'show_if_checked' => 'yes',
                    'autoload'        => true,
                ),

                array(
                    'desc'            => __( 'Admin Listener Switch', 'multitool' ),
                    'id'              => 'multitool_admin_listener_switch',
                    'default'         => 'yes',
                    'type'            => 'checkbox',
                    'checkboxgroup'   => 'end',
                    'show_if_checked' => 'yes',
                    'autoload'        => true,
                ),
                                    
                array(
                    'type'     => 'sectionend',
                    'id'     => 'multitool_listener_control_settings'
                ),                
            ));
        }

        return apply_filters( 'multitool_get_settings_' . $this->id, $settings, $current_section );
    }
}

endif;

return new Multitool_Plugin_Settings();
