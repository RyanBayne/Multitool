<?php
/**
 * Multitool - Primary Sidebar Widgets File
 *
 * @author   Ryan Bayne
 * @category Widgets
 * @package  Multitool/Widgets
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include widget classes.
//include_once( 'abstracts/abstract-multitool-widget.php' );

/**
 * Register Widgets.
 */
function multitool_register_widgets() {
    //register_widget( 'Multitool_Widget_Example' );
}
add_action( 'widgets_init', 'multitool_register_widgets' );