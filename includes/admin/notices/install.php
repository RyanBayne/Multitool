<?php
/**
 * Admin View: Notice - Install with wizard start button.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated multitool-message multitool-connect">
    <p><?php _e( '<strong>Welcome to WordPress Seed</strong> &#8211; You&lsquo;re almost ready to begin using the plugin.', 'multitool' ); ?></p>
    <p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=multitool-setup' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'multitool' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'multitool-hide-notice', 'install' ), 'multitool_hide_notices_nonce', '_multitool_notice_nonce' ) ); ?>"><?php _e( 'Skip Setup', 'multitool' ); ?></a></p>
</div>
