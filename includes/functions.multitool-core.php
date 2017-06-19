<?php
/**
 * Multitool - Core Functions
 *
 * Place a function here when it is doesn't make sense in other files or needs
 * to be obviously available to third-party developers. 
 * 
 * @author   Ryan Bayne
 * @category Core
 * @package  Multitool/Core
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} 

// Include core functions (available in both admin and frontend).
include( 'functions.multitool-formatting.php' );

/**
 * is_ajax - Returns true when the page is loaded via ajax.
 * 
 * The DOING_AJAX constant is set by WordPress.
 * 
 * @return bool
 */
function multitool_is_ajax() {          
    return defined( 'DOING_AJAX' );
}
    
/**
* Check if the home URL (stored during WordPress installation) is HTTPS. 
* If it is, we don't need to do things such as 'force ssl'.
*
* @return bool
*/
function multitool_is_https() {      
    return false !== strstr( get_option( 'home' ), 'https:' );
}

/**
* Determine if on the dashboard page. 
* 
* $current_screen is not set early enough for calling in some actions. So use this
* function instead.
*/
function multitool_is_dashboard() {      
    global $pagenow;
    // method one: check $pagenow value which could be "index.php" and that means the dashboard
    if( isset( $pagenow ) && $pagenow == 'index.php' ) { return true; }
    // method two: should $pagenow not be set, check the server value
    return strstr( $this->PHP->currenturl(), 'wp-admin/index.php' );
}

/**
* Use to check for Ajax or XMLRPC request. Use this function to avoid
* running none urgent tasks during existing operations and demanding requests.
*/
function multitool_is_background_process() {        
    if ( ( 'wp-login.php' === basename( $_SERVER['SCRIPT_FILENAME'] ) )
            || ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST )
            || ( defined( 'DOING_CRON' ) && DOING_CRON )
            || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
                return true;
    }
               
    return false;
}

/**
 * Output any queued javascript code in the footer.
 */
function multitool_print_js() {
    global $multitool_queued_js;

    if ( ! empty( $multitool_queued_js ) ) {
        // Sanitize.
        $multitool_queued_js = wp_check_invalid_utf8( $multitool_queued_js );
        $multitool_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $multitool_queued_js );
        $multitool_queued_js = str_replace( "\r", '', $multitool_queued_js );

        $js = "<!-- Multitool JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $multitool_queued_js });\n</script>\n";

        /**
         * multitool_queued_js filter.
         *
         * @since 2.6.0
         * @param string $js JavaScript code.
         */
        echo apply_filters( 'multitool_queued_js', $js );

        unset( $multitool_queued_js );
    }
}

/**
 * Display a WordPress Seed help tip.
 *
 * @since  2.5.0
 *
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function multitool_help_tip( $tip, $allow_html = false ) {
    if ( $allow_html ) {
        $tip = multitool_sanitize_tooltip( $tip );
    } else {
        $tip = esc_attr( $tip );
    }

    return '<span class="multitool-help-tip" data-tip="' . $tip . '"></span>';
}                 

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function multitool_enqueue_js( $code ) {
    global $multitool_queued_js;

    if ( empty( $multitool_queued_js ) ) {
        $multitool_queued_js = '';
    }

    $multitool_queued_js .= "\n" . $code . "\n";
}

/**
* Get all users with a specific role. 
* 
* @link https://sltaylor.co.uk/blog/get-wordpress-users-by-role/
* 
* @version 1.0 
* @param mixed $roles
*/
function multitool_getUsersByRole( $roles ) {
    global $wpdb;
    
    if ( ! is_array( $roles ) ) {
        $roles = explode( ",", $roles );
        array_walk( $roles, 'trim' );
    }
    
    $sql = "
        SELECT  ID, display_name
        FROM        " . $wpdb->users . " INNER JOIN " . $wpdb->usermeta . "
        ON      " . $wpdb->users . ".ID = " . $wpdb->usermeta . ".user_id
        WHERE   " . $wpdb->usermeta . ".meta_key = '" . $wpdb->prefix . "capabilities'
        AND     (
    ";
    
    $i = 1;
    foreach ( $roles as $role ) {       
        $sql .= ' ' . $wpdb->usermeta . '.meta_value LIKE "%' . $role . '%" ';
        if ( $i < count( $roles ) ) $sql .= ' OR ';
        $i++;
    }
    
    $sql .= ' ) ';
    $sql .= ' ORDER BY display_name ';
    $userIDs = $wpdb->get_col( $sql );
    
    return $userIDs;
}

/**
 * What type of request is this?
 *
 * Functions and constants are WordPress core. This function will allow
 * you to avoid large operations or output at the wrong time.
 * 
 * @param  string $type admin, ajax, cron or frontend.
 * @return bool
 */
function multitool_is_request( $type ) {
    switch ( $type ) {
        case 'admin' :
            return is_admin();
        case 'ajax' :
            return defined( 'DOING_AJAX' );
        case 'cron' :
            return defined( 'DOING_CRON' );
        case 'frontend' :
            return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
    }
}