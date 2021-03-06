<?php
/**
 * Multitool - Admin Notices
 *
 * Management of notice data and arguments controlling notice presentation. 
 *
 * @author   Ryan Bayne
 * @category User Interface
 * @package  Multitool/Notices
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'Multitool_Admin_Notices') ) :

class Multitool_Admin_Notices {

    /**
    * Stores notices.
    * @var array
    */
    private static $notices = array();
    
    /**
     * Array of notices - name => callback.
     * @var array
     */
    private static $core_notices = array(
        'install'             => 'install_notice',
        'update'              => 'update_notice',
    );

    /**
     * Constructor.
     * 
     * @package Multitool
     */
    public static function init() {             
        self::$notices = get_option( 'multitool_admin_notices', array() );
        add_action( 'multitool_installed', array( __CLASS__, 'reset_admin_notices' ) );
        add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
        add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );

        // Display administrator (staff) only notices.
        if ( current_user_can( 'manage_multitool' ) ) {
            add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
        }
    }
       
    /**
    * Box for presenting the status and progress of something.
    * 
    * Includes HTML5 progress bars.
    * 
    * @author Ryan Bayne
    * @package Multitool
    */    
    public function progress_box( $title, $intro, $progress_array = array() ){    
        echo '
        <div class="multitool_status_box_container">
            <div class="welcome-panel">
            
                <h3>' . ucfirst( $title ) . '</h3>
                
                <div class="welcome-panel-content">
                    <p class="about-description">' . ucfirst( $intro ) . '...</p>
                    
                    <h4>Section Development Progress</h4>

                    ' . self::info_area( '', '                    
                    Free Edition: <progress max="100" value="24"></progress> <br>
                    Premium Edition: <progress max="100" value="36"></progress> <br>
                    Support Content: <progress max="100" value="67"></progress> <br>
                    Translation: <progress max="100" value="87"></progress>' ) .'
                    <p>' . __( 'Pledge £9.99 to the Multitool project for 50% discount on the premium edition.' ) . '</p>                                                     
                </div>

            </div> 
        </div>';  
    }
    
    /**
    * Present information at the head of a page with option to dismiss.   
    * 
    * @author Ryan Bayne
    * @package Multitool
    */
    public function intro_box( $title, $intro, $info_area = false, $info_area_title = '', $info_area_content = '', $footer = false, $dismissable_id = false ){
        global $current_user;
                                               
        // handling user action - hide notice and update user meta
        if ( isset($_GET[ $dismissable_id ]) && 'dismiss' == $_GET[ $dismissable_id ] ) {
            add_user_meta( $current_user->ID, $dismissable_id, 'true', true );
            return;
        }
               
        // a dismissed intro
        if ( $dismissable_id !== false && get_user_meta( $current_user->ID, $dismissable_id ) ) {
            return;
        }
                              
        // highlighted area within the larger box
        $highlighted_info = '';
        if( $info_area == true && is_string( $info_area_title ) ) {
            $highlighted_info = '<h4>' . $info_area_title . '</h4>';
            $highlighted_info .= self::info_area( false, $info_area_content );
        }
                      
        // footer text within the larger box, smaller text, good for contact info or a link
        $footer_text = '<br>';
        if( $footer ) {
            $footer_text = '<p>' . $footer . '</p>';    
        }
        
        // add dismiss button
        $dismissable_button = '';
        if( $dismissable_id !== false ) {
            $dismissable_button = sprintf( 
                ' <a href="%s&%s=dismiss" class="button button-primary"> ' . __( 'Hide', 'multitool' ) . ' </a>', 
                $_SERVER['REQUEST_URI'], 
                $dismissable_id 
            );
        }
                
        echo '
        <div class="multitool_status_box_container">
            <div class="welcome-panel">
                <div class="welcome-panel-content">
                    
                    <h1>' . ucfirst( $title ) . $dismissable_button . '</h1>
                    
                    <p class="about-description">' . ucfirst( $intro ) . '</p>
 
                    ' . $highlighted_info . '
                    
                    ' . $footer_text . '
                  
                </div>
            </div> 
        </div>';  
    } 

    /**
    * A mid grey area for attracting focus to a small amount of information. Is 
    * an alternative to standard WordPress notices and good for tips. 
    * 
    * @param mixed $title
    * @param mixed $message
    * 
    * @author Ryan Bayne
    * @package Multitool 
    */
    public function info_area( $title, $message, $admin_only = true ){   
        if( $admin_only == true && current_user_can( 'manage_options' ) || $admin_only !== true){
            
            $area_title = '';
            if( $title ){
                $area_title = '<h4>' . $title . '</h4>';
            }
            
            return '
            <div style="background:#ECECEC;border:1px solid #CCC;padding:0 10px;margin-top:5px;border-radius:5px;-moz-border-radius:5px;-webkit-border-radius:5px;">
                ' . $area_title  . '
                <p>' . $message . '</p>
            </div>';          
        }
    }                          

    /**
     * Store notices to DB
     */
    public static function store_notices() {
        update_option( 'multitool_admin_notices', self::get_notices() );
    }
                                
    /**
     * Get notices
     * @return array
     */
    public static function get_notices() {
        return self::$notices;
    }
                                  
    /**
     * Remove all notices.
     */
    public static function remove_all_notices() {
        self::$notices = array();
    }

    /**
     * Show a notice.
     * @param string $name
     */
    public static function add_notice( $name ) {
        self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
    }
                                        
    /**
     * Remove a notice from being displayed.
     * @param  string $name
     */
    public static function remove_notice( $name ) {
        self::$notices = array_diff( self::get_notices(), array( $name ) );
        delete_option( 'multitool_admin_notice_' . $name );
    }

    /**
     * When theme is switched or new version detected, reset notices.
     */
    public static function reset_admin_notices() {

    }
        
    /**
     * See if a notice is being shown.
     * @param  string  $name
     * @return boolean
     */
    public static function has_notice( $name ) {
        return in_array( $name, self::get_notices() );
    }
                                       
    /**
     * Hide a notice if the GET variable is set.
     */
    public static function hide_notices() {
        if ( isset( $_GET['multitool-hide-notice'] ) && isset( $_GET['_multitool_notice_nonce'] ) ) {
            if ( ! wp_verify_nonce( $_GET['_multitool_notice_nonce'], 'multitool_hide_notices_nonce' ) ) {
                wp_die( __( 'Action failed. Please refresh the page and retry.', 'multitool' ) );
            }

            if ( ! current_user_can( 'manage_multitool' ) ) {
                wp_die( __( 'Cheatin&#8217; huh?', 'multitool' ) );
            }

            $hide_notice = sanitize_text_field( $_GET['multitool-hide-notice'] );
            self::remove_notice( $hide_notice );
            do_action( 'multitool_hide_' . $hide_notice . '_notice' );
        }
    }
                                         
    /**
     * Add notices + styles if needed.
     */
    public static function add_notices() {
        $notices = self::get_notices();

        if ( ! empty( $notices ) ) {
            wp_enqueue_style( 'multitool-activation', plugins_url(  '/assets/css/activation.css', MULTITOOL_PLUGIN_FILE ) );
            foreach ( $notices as $notice ) {
                if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'multitool_show_admin_notice', true, $notice ) ) {
                    add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
                } else {
                    add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
                }
            }
        }
    }

    /**
     * Add a custom notice.
     * 
     * Example: Multitool_Admin_Notices::add_custom_notice( 'mycustomnotice', 'My name is <strong>Ryan Bayne</strong>' );
     * 
     * @param string $name
     * @param string $notice_html
     */
    public static function add_custom_notice( $name, $notice_html ) {
        self::add_notice( $name );
        update_option( 'multitool_admin_notice_' . $name, wp_kses_post( $notice_html ) );
    }
                                        
    /**
     * Output any stored custom notices.
     */
    public static function output_custom_notices() {
        $notices = self::get_notices();

        if ( ! empty( $notices ) ) {
            foreach ( $notices as $notice ) {
                if ( empty( self::$core_notices[ $notice ] ) ) {
                    $notice_html = get_option( 'multitool_admin_notice_' . $notice );

                    if ( $notice_html ) {
                        include( 'notices/custom.php' );
                    }
                }
            }
        }
    }                               

    /**
     * If we need to update, include a message with the update button.
     */
    public static function update_notice() {
        if ( version_compare( get_option( 'multitool_db_version' ), MULTITOOL_VERSION, '<' ) ) {
            $updater = new Multitool_Background_Updater();
            if ( $updater->is_updating() || ! empty( $_GET['do_update_multitool'] ) ) {
                include( 'notices/updating.php' );
            } else {
                include( 'notices/update.php' );
            }
        } else {
            include( 'notices/updated.php' );
        }
    }

    /**
     * If we have just installed, show a message with the install pages button.
     */
    public static function install_notice() {
        include( 'notices/install.php' );
    }
    
    public static function notice( $type, $title, $description, $dismissible = false ) {
        self::$type( $title, $description, $dismissible );    
    }
    
    public static function error( $title, $desc, $dismissible = false ) {
        $d = ''; if( $dismissible ){ $d = ' is-dismissible'; }
        ?><div class="notice notice-error<?php echo $d; ?>>"><p><?php echo '<strong>' . $title . ': </strong>' . $desc; ?>.</p></div><?php     
    } 
    
    public static function warning( $title, $desc, $dismissible = false ) {
        $d = ''; if( $dismissible ){ $d = ' is-dismissible'; }
        ?><div class="notice notice-warning<?php echo $d; ?>"><p><?php echo '<strong>' . $title . ': </strong>' . $desc; ?>.</p></div><?php     
    } 
    
    public static function success( $title, $desc, $dismissible = false ) {
        $d = ''; if( $dismissible ){ $d = ' is-dismissible'; }
        ?><div class="notice notice-success<?php echo $d; ?>"><p><?php echo '<strong>' . $title . ': </strong>' . $desc; ?>.</p></div><?php     
    } 
    
    public static function info( $title, $desc, $dismissible = false ) {
        $d = ''; if( $dismissible ){ $d = ' is-dismissible'; }
        ?><div class="notice notice-info<?php echo $d; ?>"><p><?php echo '<strong>' . $title . ': </strong>' . $desc; ?>.</p></div><?php     
    } 
}

endif;

Multitool_Admin_Notices::init();