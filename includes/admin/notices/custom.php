<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated multitool-message">
    <a class="multitool-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'multitool-hide-notice', $notice ), 'multitool_hide_notices_nonce', '_multitool_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'multitool' ); ?></a>
    <?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>
