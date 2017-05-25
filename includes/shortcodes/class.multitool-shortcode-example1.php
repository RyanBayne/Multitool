<?php
/**
 * Multitool - Example Shortcode 
 *
 * @author   Ryan Bayne
 * @category Frontend
 * @package  Multitool/Shortcodes
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * My appointments list.
 */
class App_Shortcode_MyAppointments extends App_Shortcode {
    public function __construct () {
        $this->name = __( 'My Appointments', 'appointments' );
    }

    public function get_defaults() {                 
        return array(                              
            'title' => array(
                'type' => 'text',
                'name' => __( 'Text Example', 'multitool' ),
                'value' => __('<h3>Test Example</h3>', 'multitool'),
                'help' => __('Help example for text.', 'multitool'),
            ),
            'order' => array(
                'type' => 'select',
                'name' => __( 'Order', 'multitool' ),
                'options' => array(
                    array( 'text' => 'Ascendant', 'value' => 'asc' ),
                    array( 'text' => 'Descendant', 'value' => 'desc' ),
                ),
                'value' => 'asc',
                'help' => __('Help example for text.', 'multitool'),
            ),
            'allow_cancel' => array(
                'type' => 'checkbox',
                'name' => __( 'Allow Cancellation', 'multitool' ),
                'value' => 0,
                'help' => __('Help example for text.', 'multitool'),
            ),
        );
    }

    public function get_usage_info () {             
        return __('Inserts a table where client or service provider can see his upcoming appointments.', 'multitool');
    }

    public function process_shortcode ( $args=array(), $content='' ) {       
        $args = wp_parse_args($args, $this->_defaults_to_args());
    }
}