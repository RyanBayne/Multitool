<?php
/**
 * Admin View: Notice - Updated
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated multitool-message multitool-connect">
    <a class="multitool-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'multitool-hide-notice', 'update', remove_query_arg( 'do_update_multitool' ) ), 'multitool_hide_notices_nonce', '_multitool_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'multitool' ); ?></a>

    <p><?php _e( 'Multitool data update complete. Thank you for updating to the latest version!', 'multitool' ); ?></p>
</div>
