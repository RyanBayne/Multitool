<?php
/**
 * Multitool Plugin Tools
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
 * Multitool_Plugin_Tools.
 */
class Multitool_Plugin_Tools extends Multitool_Settings_Page {

    /**
     * Constructor.
     */
    public function __construct() {

        $this->id    = 'plugintools';
        $this->label = __( 'Plugin Tools', 'multitool' );

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

        $settings = apply_filters( 'multitool_plugin_tools_settings', array(

            array( 'title' => __( 'Plugins Snapshot', 'multitool' ), 
                   'type' => 'title', 
                   'desc' => __( 'Take a snapshot of your current plugins and their status. Also activates a Quick Tool for re-installing all your plugins with one click.', 'multitool' ), 
                   'id' => 'multitool_configtool_pluginssnapshot' 
            ),
            
            array(
                'title'   => __( 'Activate Tool', 'woocommerce' ),
                'desc'    => __( 'Activates the Plugins Snapshot tool and makes a Quick Tool available.', 'multitool' ),
                'id'      => 'pluginsnapshot_activate',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
            
            array(
                'title'   => __( 'Download Plugin List', 'woocommerce' ),
                'desc'    => __( 'Your list of plugins will be added to a .txt file and downloaded by your browser.', 'multitool' ),
                'id'      => 'pluginsnapshot_downloadtxt',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
                        
            array(
                'title'   => __( 'Update Snapshot', 'woocommerce' ),
                'desc'    => __( 'Automatically updats your previous snapshot if a new plugin is installed since.', 'multitool' ),
                'id'      => 'pluginsnapshot_autoupdate',
                'default' => 'no',
                'type'    => 'checkbox',
            ),
                    
            array( 
                'type' => 'sectionend', 
                'id' => 'multitool_configtool_pluginsnapshot'),
            
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

return new Multitool_Plugin_Tools();
