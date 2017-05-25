<?php
/**
 * Multitool Quick Tools Class
 * 
 * Contains methods for each quick tool. 
 * 
 * Remember that the quicktools table might be displaying cached data.
 * 
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_QuickTools' ) ) :

/**
 * Multitool_QuickTools.
 * 
 * When making changes please remember that the quicktools 
 * table might be displaying cached data.
 * 
 * Append tool methods with "tool_". 
 */
class Multitool_QuickTools {
    /**
    * Change to true and iterate through all methods for info.
    * 
    * @var mixed
    */
    public $return_tool_info = false;
    
    /**
    * Mainly for hooks. 
    */
    public static function init() {           
        add_action( 'admin_init', array( __CLASS__, 'listener' )  );     
    }
    
    /**
    * Listens for tools being used on the Quick Tools table view.
    * 
    * Hooked by "init" in the init() method.
    * 
    * If a tool needs to send the user elsewhere, handle it by forwarding
    * them using a method in this class. Ensuring a standard approach to
    * every tools security checks and validation.
    *
    * @version 1.0
    */
    public static function listener() {    
        if( !isset( $_REQUEST['_wpnonce'] ) ) {
            return;
        }     
        
        if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'quicktool_action' ) ) {
            return;
        } 
        
        if( !isset( $_GET['toolname'] ) ) {  
            return;
        }
        $tool_name = $_GET['toolname'];
             
        if( !method_exists( __CLASS__, $tool_name ) ) {       
            return;
        }    
        
        $QuickTools = new Multitool_QuickTools();                 
        $QuickTools->return_tool_info = true;
        
        eval( '$tool_info = $QuickTools->$tool_name();');
        
        if( !isset( $tool_info['capability'] ) ) {
            return;
        }
        
        if( !current_user_can( $tool_info['capability'] ) ) {
            return;
        }
        
        // Ensure the request is attempting to use an actual tool!
        if( substr( $tool_name, 0, 5 ) !== "tool_" ) { 
            return; 
        }
        
        $QuickTools->return_tool_info = false;
        $QuickTools->$tool_name();
    }
    
    public function get_categories() {
        return $tool_categories = array( 'posts', 'users', 'comments', 'plugins', 'security', 'seo', 'social', 'integration' );    
    }
    
    /**
    * Display a list of the latest subscribers. A maximum of 100.
    * 
    * @version 1.0
    */
    public function tool_display_latest_subscribers() {
        $tool_info = array(
            'title'       => 'Display Latest Subscribers',
            'description' => __( 'Displays usernames and email addresses for the latest registered users.', 'multitool' ),
            'version'     => '1.0',
            'author'      => 'Ryan Bayne',
            'url'         => '',
            'category'    => 'users',
            'capability'  => 'activate_plugins'
        );
        
        if( $this->return_tool_info ){ return $tool_info; }    
        
        if( !current_user_can( $tool_info['capability'] ) ) { return; }
        
        $blogusers = get_users( array( 'fields' => array( 'ID', 'display_name', 'user_email' ) ) );
        foreach ( $blogusers as $user ) {
            wp_die( '<p>ID: ' . esc_html( $user->ID ) . ' - Display Name: ' . esc_html( $user->display_name ) . ' - Email: ' . esc_html( $user->user_email ) . '</p>' );
        }
    }   
    
    /**
    * Sends the user to the latest wp_post (post,page,custom post types).
    * 
    * @version 1.0 
    */
    public function tool_go_to_latest_publication() {
        $tool_info = array(
            'title'       => 'View Latest Publication',
            'description' => __( 'Display information about the latest authored post including pages and custom post-types.', 'multitool' ),
            'version'     => '1.0',
            'author'      => 'Ryan Bayne',
            'url'         => '',
            'category'    => 'users',
            'capability'  => 'activate_plugins'
        );
        
        if( $this->return_tool_info ){ return $tool_info; }     
        
        if( !current_user_can( $tool_info['capability'] ) ) { return; }
        
        $args = array(
            'numberposts' => 1,
            'orderby' => 'post_date',
            'order' => 'DESC',
            'post_type' => array( 'post', 'page' ),
            'post_status' => 'draft, publish, future, pending, private',
            'suppress_filters' => true
        );

        $recent_posts = wp_get_recent_posts( $args, ARRAY_A ); 
        
        echo '<pre>';
        var_dump( $recent_posts ); 
        echo '</pre>';       
    }  
}

endif;

$QuickTools = new Multitool_QuickTools();
$QuickTools->init();
unset($QuickTools);