<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
<div id="message" class="updated multitool-message multitool-connect">
    <p><strong><?php _e( 'Multitool Data Update', 'multitool' ); ?></strong> &#8211; <?php _e( 'We need to update your store\'s database to the latest version.', 'multitool' ); ?></p>
    <p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_multitool', 'true', admin_url( 'admin.php?page=multitool-settings' ) ) ); ?>" class="multitool-update-now button-primary"><?php _e( 'Run the updater', 'multitool' ); ?></a></p>
</div>
<script type="text/javascript">
    jQuery( '.multitool-update-now' ).click( 'click', function() {
        return window.confirm( '<?php echo esc_js( __( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'multitool' ) ); ?>' ); // jshint ignore:line
    });
</script>
