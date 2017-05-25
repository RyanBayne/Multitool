<?php
/**
 * Multitool - SPL Autoloader Class
 *
 * @author   Ryan Bayne
 * @category System
 * @package  Multitool/System
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}    

/**
* WP Seed SPL Autoloader Class
*/
class Multitool_Autoloader {

    /**
     * Path to the includes directory.
     *
     * @var string
     */
    private $include_path = '';

    /**
     * The Constructor.
     */
    public function __construct() {
        if ( function_exists( "__autoload" ) ) {
            spl_autoload_register( "__autoload" );
        }

        spl_autoload_register( array( $this, 'autoload' ) );

        $this->include_path = untrailingslashit( plugin_dir_path( MULTITOOL_PLUGIN_FILE ) ) . '/includes/';
    }

    /**
     * Take a class name and turn it into a file name.
     *
     * @param  string $class
     * @return string
     */
    private function get_file_name_from_class( $class ) {
        return 'class.' . str_replace( '_', '-', $class ) . '.php';
    }

    /**
     * Include a class file.
     *
     * @param  string $path
     * @return bool successful or not
     */
    private function load_file( $path ) {
        if ( $path && is_readable( $path ) ) {
            include_once( $path );
            return true;
        }
        return false;
    }

    /**
     * Auto-load Multitool classes on demand to reduce memory consumption.
     *
     * @param string $class
     */
    public function autoload( $class ) {
        $class = strtolower( $class );
        $file  = $this->get_file_name_from_class( $class );
        $path  = '';
   
        if ( strpos( $class, 'multitool_shortcode_' ) === 0 ) {
            $path = $this->include_path . 'shortcodes/';
        } elseif ( strpos( $class, 'multitool_meta_box' ) === 0 ) {
            $path = $this->include_path . 'admin/meta-boxes/';
        } elseif ( strpos( $class, 'multitool_admin' ) === 0 ) {
            $path = $this->include_path . 'admin/';
        } 
    
        if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'multitool-' ) === 0 ) ) {
            $this->load_file( $this->include_path . $file );
        }
    }
}

new Multitool_Autoloader();