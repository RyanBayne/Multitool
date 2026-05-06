<?php
/**
 * Multitool - Admin Only Functions
 *
 * This file will only be included during an admin request. Use a file
 * like functions.multitool-core.php if your function is meant for the frontend.   
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  Multitool/Admin
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Generate the complete nonce string, from the nonce base, the action 
 * and an item, e.g. multitool_delete_table_3.
 *
 * @since 1.0.0
 *
 * @param string      $action Action for which the nonce is needed.
 * @param string|bool $item   Optional. Item for which the action will be performed, like "table".
 * @return string The resulting nonce string.
 */
function multitool_nonce_prepend( $action, $item = false ) {
    $nonce = "multitool_{$action}";
    if ( $item ) {
        $nonce .= "_{$item}";
    }
    return $nonce;
}

/**
 * Get all WordPress Seed screen ids.
 *
 * @return array
 */
function multitool_get_screen_ids() {

    $screen_ids   = array(
        'tools_page_multitool-quick',
        'tools_page_multitool-config',
        'tools_page_multitool-advanced',
    );

    return apply_filters( 'multitool_screen_ids', $screen_ids );
}