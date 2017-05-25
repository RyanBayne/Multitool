<?php
/**
 * Multitool Configuration Tools Class
 * 
 * Contains methods for each quick tool. 
 * 
 * @author      Ryan Bayne
 * @category    Admin
 * @package     Multitool/Admin
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'Multitool_ConfigurationTools' ) ) :

/**
 * Multitool_ConfigurationTools.
 * 
 * When making changes please remember that the quicktools 
 * table might be displaying cached data.
 * 
 * Append tool methods with "tool_". 
 */
class Multitool_ConfigurationTools {
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
        add_action( 'wp_login', array( __CLASS__, 'hook_wp_login' ), 1, 2 );     
    }
    
    /**
    * Any configuration tool methods that require the "wp_login" hook should
    * be called in this method.
    * 
    * @version 1.0
    */
    public static function hook_wp_login( $user_login, $user ) {
      
        self::tool_administration_account_limit( $user );     
      
    }
    
    /**
    * Enforces a limit on the number of administration accounts. 
    * 
    * @version 1.0 
    */
    public static function tool_administration_account_limit() {
        if( !$opt_adminaccounts_active = get_option( 'adminaccountscap_activate') ) {
            return false;
        }        
    
        if( $opt_adminaccounts_active !== 'yes' ) {
            return false;
        }
        
        // We need a limit (two or more).
        if( !$opt_adminaccounts_limit = get_option( 'adminaccountscap_limit' ) ) {
            return;    
        }
        
        if( !is_numeric( $opt_adminaccounts_limit ) || $opt_adminaccounts_limit === 1 ) {
            return;    
        }
        
        // First we determine if there are extra administrators.          
        $result = count_users();
        foreach( $result['avail_roles'] as $role => $count ) {    
            if( $role == 'administrator' ) { 
                if( $count == $opt_adminaccounts_limit ) {
                    // There are extra admin accounts! Red Alert! Begin a report...
                    $report = '';
                    $adminIDs = multitool_getUsersByRole( array( 'administrator') );
                    // Unset all of the expect accounts.
                    $i = 0;
                    foreach( $adminIDs as $key => $ID ) {
                        if( $ID == 1 ) {
                            //...because we never block out the owner!
                            unset( $adminIDs[ $key ]);
                        } elseif( $i >= $opt_adminaccounts_limit ) {
                            //...disable this extra potentially hacker injected account.
                            $user_obj = new WP_User( $ID );

                            // Remove role
                            $user_obj->remove_role( 'administrator' );

                            // Add role
                            $user_obj->add_role( 'subscriber' );                            
                        }        
                        ++$i;
                    }     
                    // Still to apply the adminaccountscap_alerttype option.
                    Multitool_Admin_Notices::add_custom_notice( 'adminaccountlimitreport', $report );
                }
                break;  
            }
        }  
    }
}

endif;

$ConfigurationTools = new Multitool_ConfigurationTools();
$ConfigurationTools->init();
unset($ConfigurationTools);