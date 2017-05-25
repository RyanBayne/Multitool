<?php
/**
 * Plugin Name: Multitool
 * Plugin URI: https://www.wordpress.org/plugins/multitool
 * Github URI: https://github.com/ryanbayne/multitool
 * Description: The Swiss-Army-Plugin for WordPress. 
 * Version: 1.2.0
 * Author: Ryan Bayne
 * Author URI: https://multitool.wordpress.com/
 * Requires at least: 4.4
 * Tested up to: 4.7
 * License: GPL3
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /i18n/languages/
 * 
 * @package Multitool
 * @author Ryan Bayne
 * @license GNU General Public License, Version 3
 * @copyright 2016-2017 Ryan R. Bayne (SqueekyCoder@Gmail.com)
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
                 
if ( ! class_exists( 'WordPressMultitool' ) ) :

/**
 * Main Multitool Class.
 *
 * @class Multitool
 * @version 1.0.0
 */
final class WordPressMultitool {
    
    /**
     * Multitool version.
     *
     * @var string
     */
    public $version = '1.2.0';

    /**
     * Minimum WP version.
     *
     * @var string
     */
    public $min_wp_version = '4.4';
    
    /**
     * The single instance of the class.
     *
     * @var Multitool
     * @since 2.1
     */
    protected static $_instance = null;

    /**
     * Session instance.
     *
     * @var Multitool_Session
     */
    public $session = null; 
        
    /**
     * Main Multitool Instance.
     *
     * Ensures only one instance of Multitool is loaded or can be loaded.
     *
     * @since 1.0
     * @static
     * @see WordPressSeed()
     * @return Multitool - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }                    
        return self::$_instance;
    }

    /**
     * Cloning Multitool is forbidden.
     * @since 1.0
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Your not allowed to do that!', 'multitool' ), '1.0' );
    }

    /**
     * Unserializing instances of this class is forbidden.
     * @since 1.0
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Your not allowed to do that!', 'multitool' ), '1.0' );
    }

    /**
     * Auto-load in-accessible properties on demand.
     * @param mixed $key
     * @return mixed
     */
    public function __get( $key ) {
        if ( in_array( $key, array( 'mailer' ) ) ) {
            return $this->$key();
        }
    }   
    
    /**
     * Multitool Constructor.
     */
    public function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();

        do_action( 'multitool_loaded' );
    }

    /**
     * Hook into actions and filters.
     * @since  1.0
     */
    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'Multitool_Install', 'install' ) );
        // Do not confuse deactivation of a plugin with deletion of a plugin - two very different requests.
        register_deactivation_hook( __FILE__, array( 'Multitool_Install', 'deactivate' ) );
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * Define Multitool Constants.
     */
    private function define_constants() {
        
        $upload_dir = wp_upload_dir();
        
        // Main (package) constants.
        if ( ! defined( 'MULTITOOL_PLUGIN_FILE' ) ) { define( 'MULTITOOL_PLUGIN_FILE', __FILE__ ); }
        if ( ! defined( 'MULTITOOL_PLUGIN_BASENAME' ) ) { define( 'MULTITOOL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) ); }
        if ( ! defined( 'MULTITOOL_PLUGIN_DIR_PATH' ) ) { define( 'MULTITOOL_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) ); }
        if ( ! defined( 'MULTITOOL_VERSION' ) ) { define( 'MULTITOOL_VERSION', $this->version ); }
        if ( ! defined( 'MULTITOOL_MIN_WP_VERSION' ) ) { define( 'MULTITOOL_MIN_WP_VERSION', $this->min_wp_version ); }
        if ( ! defined( 'MULTITOOL_LOG_DIR' ) ) { define( 'MULTITOOL_LOG_DIR', $upload_dir['basedir'] . '/multitool-logs/' ); }
        if ( ! defined( 'MULTITOOL_SESSION_CACHE_GROUP' ) ) { define( 'MULTITOOL_SESSION_CACHE_GROUP', 'multitool_session_id' ); }
        if ( ! defined( 'MULTITOOL_DEV_MODE' ) ) { define( 'MULTITOOL_DEV_MODE', false ); }
        if ( ! defined( 'MULTITOOL_WORDPRESSORG_SLUG' ) ) { define( 'MULTITOOL_WORDPRESSORG_SLUG', false ); }
        if ( ! defined( 'MULTITOOL_MARKETPLACE' ) ) { define( 'MULTITOOL_MARKETPLACE', false ); }
        if ( ! defined( 'MULTITOOL_MARKETPLACE_ID' ) ) { define( 'MULTITOOL_MARKETPLACE_ID', false ); }
                                      
        // Support (project) constants.
        if ( ! defined( 'MULTITOOL_HOME' ) ) { define( 'MULTITOOL_HOME', 'https://multitool.wordpress.com/' ); }
        if ( ! defined( 'MULTITOOL_FORUM' ) ) { define( 'MULTITOOL_FORUM', 'https://multitool.slack.com/' ); }
        if ( ! defined( 'MULTITOOL_TWITTER' ) ) { define( 'MULTITOOL_TWITTER', false ); }
        if ( ! defined( 'MULTITOOL_TRELLO' ) ) { define( 'MULTITOOL_TRELLO', 'https://trello.com/b/aFGDfb8T/wp-multitool' ); }
        if ( ! defined( 'MULTITOOL_DONATE' ) ) { define( 'MULTITOOL_DONATE', 'https://www.patreon.com/ryanbayne' ); }
        if ( ! defined( 'MULTITOOL_SKYPE' ) ) { define( 'MULTITOOL_SKYPE', 'https://join.skype.com/bVtDaGHd9Nnl' ); }
        if ( ! defined( 'MULTITOOL_GITHUB' ) ) { define( 'MULTITOOL_GITHUB', 'https://github.com/RyanBayne/multitool' ); }
        if ( ! defined( 'MULTITOOL_DEMOSITE' ) ) { define( 'MULTITOOL_DEMOSITE', false ); };
        if ( ! defined( 'MULTITOOL_SLACK' ) ) { define( 'MULTITOOL_SLACK', 'https://ryanbayne.slack.com/messages/C5FSX5WHG/details/' ); }
        if ( ! defined( 'MULTITOOL_DOCS' ) ) { define( 'MULTITOOL_DOCS', 'https://github.com/RyanBayne/multitool/wiki' ); }
        if ( ! defined( 'MULTITOOL_FACEBOOK' ) ) { define( 'MULTITOOL_FACEBOOK', 'https://www.facebook.com/WordPress-Plugin-Seed-704154249757165/' ); }
       
        // Author (social) constants - can act as default when support constants are false.                                                                                                              
        if ( ! defined( 'MULTITOOL_AUTHOR_HOME' ) ) { define( 'MULTITOOL_AUTHOR_HOME', 'https://www.linkedin.com/in/ryanrbayne/' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_FORUM' ) ) { define( 'MULTITOOL_AUTHOR_FORUM', false ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_TWITTER' ) ) { define( 'MULTITOOL_AUTHOR_TWITTER', 'http://www.twitter.com/Ryan_R_Bayne' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_TRELLO' ) ) { define( 'MULTITOOL_AUTHOR_TRELLO', 'https://trello.com/ryanrbayne1' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_FACEBOOK' ) ) { define( 'MULTITOOL_AUTHOR_FACEBOOK', 'https://www.facebook.com/ryanrbayne' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_DONATE' ) ) { define( 'MULTITOOL_AUTHOR_DONATE', 'https://www.patreon.com/ryanbayne' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_SKYPE' ) ) { define( 'MULTITOOL_AUTHOR_SKYPE', 'https://join.skype.com/gNuxSa4wnQTV' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_GITHUB' ) ) { define( 'MULTITOOL_AUTHOR_GITHUB', 'https://github.com/RyanBayne' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_LINKEDIN' ) ) { define( 'MULTITOOL_AUTHOR_LINKEDIN', 'https://www.linkedin.com/in/ryanrbayne/' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_DISCORD' ) ) { define( 'MULTITOOL_AUTHOR_DISCORD', 'https://discord.gg/PcqNqNh' ); }
        if ( ! defined( 'MULTITOOL_AUTHOR_SLACK' ) ) { define( 'MULTITOOL_AUTHOR_SLACK', 'https://ryanbayne.slack.com/threads/team/' ); }
    }

    /**
     * Include required core files used in admin and on the frontend.
     */
    public function includes() {
        
        include_once( 'includes/functions.multitool-core.php' );
        include_once( 'includes/class.multitool-debug.php' );    
        include_once( 'includes/class.multitool-autoloader.php' );
        include_once( 'includes/functions.multitool-validate.php' );        
        include_once( 'includes/class.multitool-install.php' );
        include_once( 'includes/class.multitool-ajax.php' );
        include_once( 'includes/class.multitool-configurationtools.php' );
        
        if ( $this->is_request( 'admin' ) ) {
            include_once( 'includes/admin/class.multitool-admin.php' );
        }

        if ( $this->is_request( 'frontend' ) ) {
            $this->frontend_includes();
        }
    }

    /**
     * Include required frontend files.
     */
    public function frontend_includes() {
        include_once( 'includes/class.multitool-frontend-scripts.php' );  
    }

    /**
     * Initialise WordPress Plugin Seed when WordPress Initialises.
     */
    public function init() {                     
        // Before init action.
        do_action( 'before_multitool_init' );

        // Init action.
        do_action( 'multitool_init' );
    }

    /**
     * Get the plugin url.
     * @return string
     */
    public function plugin_url() {                
        return untrailingslashit( plugins_url( '/', __FILE__ ) );
    }

    /**
     * Get the plugin path.
     * @return string
     */
    public function plugin_path() {              
        return untrailingslashit( plugin_dir_path( __FILE__ ) );
    }

    /**
     * Get Ajax URL (this is the URL to WordPress core ajax file).
     * @return string
     */
    public function ajax_url() {                
        return admin_url( 'admin-ajax.php', 'relative' );
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
    private function is_request( $type ) {
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
}

endif;

if( !function_exists( 'GlobalMultitool' ) ) {
    /**
     * Main instance of WordPress Plugin Seed.
     *
     * Returns the main instance of Multitool to prevent the need to use globals.
     *
     * @since  1.0
     * @return Multitool
     */
    function GlobalMultitool() {
        return WordPressMultitool::instance();
    }

    // Global for backwards compatibility.
    $GLOBALS['multitool'] = GlobalMultitool();
    
    //$multitool_debug = new Multitool_Debug();
    //$multitool_debug->debugmode();   
}

