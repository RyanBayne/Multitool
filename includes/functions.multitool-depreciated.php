<?php
/**
 * Multitool - Depreciated Functions
 *
 * Please add the WordPress core function for triggering and error if a
 * depreciated function is used. 
 * 
 * Use: _deprecated_function( 'multitool_function_called', '2.1', 'multitool_replacement_function' );  
 *
 * @author   Ryan Bayne
 * @category Core
 * @package  Multitool/Core
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 
  
/**
 * @deprecated example only
 */
function multitool_function_called() {
    _deprecated_function( 'multitool_function_called', '2.1', 'multitool_replacement_function' );
    //multitool_replacement_function();
}