<?php
/**
 * Multitool - Developer Toolbar
 *
 * The developer toolbar requires the "seniordeveloper" custom capability. The
 * toolbar allows actions not all key holders should be giving access to. The
 * menu is intended for developers to already have access to a range of
 *
 * @author   Ryan Bayne
 * @category Admin
 * @package  Multitool/Toolbars
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}  

if( !class_exists( 'Multitool_Admin_Toolbar_Developers' ) ) :

class Multitool_Admin_Toolbar_Developers {
    public function __construct() {
        if( !current_user_can( 'seniordeveloper' ) ) return false;
        $this->init(); 
    }    
    
    private function init() {
        global $wp_admin_bar, $multitool_settings;  
        
        // Top Level/Level One
        $args = array(
            'id'     => 'multitool-toolbarmenu-developers',
            'title'  => __( 'WP Seed Developers', 'text_domain' ),          
        );
        $wp_admin_bar->add_menu( $args );
        
            // Group - Debug Tools
            $args = array(
                'id'     => 'multitool-toolbarmenu-debugtools',
                'parent' => 'multitool-toolbarmenu-developers',
                'title'  => __( 'Debug Tools', 'text_domain' ), 
                'meta'   => array( 'class' => 'first-toolbar-group' )         
            );        
            $wp_admin_bar->add_menu( $args );

                // error display switch        
                $href = wp_nonce_url( admin_url() . 'admin.php?page=' . $_GET['page'] . '&multitoolaction=' . 'debugmodeswitch'  . '', 'debugmodeswitch' );
                if( !isset( $multitool_settings['displayerrors'] ) || $multitool_settings['displayerrors'] !== true ) 
                {
                    $error_display_title = __( 'Hide Errors', 'multitool' );
                } 
                else 
                {
                    $error_display_title = __( 'Display Errors', 'multitool' );
                }
                $args = array(
                    'id'     => 'multitool-toolbarmenu-errordisplay',
                    'parent' => 'multitool-toolbarmenu-debugtools',
                    'title'  => $error_display_title,
                    'href'   => $href,            
                );
                $wp_admin_bar->add_menu( $args );    
    }
    
}   

endif;

return new Multitool_Admin_Toolbar_Developers();
