<?php                 
/**
 * Multitool - WP Admin Dashboard
 *
 * Custom dashboard widgets and functionality goes here.  
 *
 * @author   Ryan Bayne
 * @category WordPress Dashboard
 * @package  Multitool/Admin
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Multitool_Admin_Dashboard' ) ) :

/**
 * Multitool_Admin_Dashboard Class.
 */
class Multitool_Admin_Dashboard {

    /**
     * Init dashboard widgets.
     */
    public function init() {           
        if ( current_user_can( 'activate_plugins' ) ) {
            wp_add_dashboard_widget( 'multitool_dashboard_widget_example', __( 'Example Widget', 'multitool' ), array( $this, 'example_widget' ) );
        }
    }
       
    /**
     * Recent reviews widget.
     */
    public function example_widget() {              
        echo '<p>' . __( 'This is an example widget only. A developer must use it or remove it.', 'multitool' ) . '</p>';
    }

}

endif;

return new Multitool_Admin_Dashboard();
