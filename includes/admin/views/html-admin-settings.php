<?php
/**
 * Admin View: Settings
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?>
        
<div class="wrap multitool">
    <h1>
        <?php _e( 'Multitool Configuration Tools', 'multitool' ); ?>
    </h1>
    <form method="<?php echo esc_attr( apply_filters( 'multitool_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
        <nav class="nav-tab-wrapper woo-nav-tab-wrapper">
            <?php
                foreach ( $tabs as $name => $label ) {
                    echo '<a href="' . admin_url( 'tools.php?page=multitool-config&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
                }
                do_action( 'multitool_settings_tabs' );
            ?>
        </nav>
        <h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
        <?php
            do_action( 'multitool_sections_' . $current_tab );

            self::show_messages();

            do_action( 'multitool_settings_' . $current_tab );
            do_action( 'multitool_settings_tabs_' . $current_tab ); // @deprecated hook
        ?>
        <p class="submit">
            <?php if ( empty( $GLOBALS['hide_save_button'] ) ) : ?>
                <input name="save" class="button-primary multitool-save-button" type="submit" value="<?php esc_attr_e( 'Save changes', 'multitool' ); ?>" />
            <?php endif; ?>
            <?php wp_nonce_field( 'multitool-settings' ); ?>
        </p>
    </form>
</div>
