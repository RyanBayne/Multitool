<?php
/**
 * Multitool Compatibility Bootstrap
 * 
 * Establishes the modern plugin initialization pattern while preserving
 * legacy code and functionality. Sets up service layer for tool registry.
 *
 * @package Multitool
 */

namespace Multitool\Bootstrap;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class PluginBootstrap {

    /**
     * Boot the Multitool plugin.
     *
     * @param string $plugin_file The main plugin file path.
     */
    public static function boot( $plugin_file ) {
        self::define_constants( $plugin_file );
        self::load_modern_runtime();
        self::load_legacy_runtime();
        self::register_hooks();
    }

    /**
     * Define plugin constants.
     *
     * @param string $plugin_file
     */
    private static function define_constants( $plugin_file ) {
        if ( ! defined( 'MULTITOOL_PLUGIN_FILE' ) ) {
            define( 'MULTITOOL_PLUGIN_FILE', $plugin_file );
        }
        if ( ! defined( 'MULTITOOL_PLUGIN_BASENAME' ) ) {
            define( 'MULTITOOL_PLUGIN_BASENAME', plugin_basename( $plugin_file ) );
        }
        if ( ! defined( 'MULTITOOL_PLUGIN_DIR_PATH' ) ) {
            define( 'MULTITOOL_PLUGIN_DIR_PATH', plugin_dir_path( $plugin_file ) );
        }
        if ( ! defined( 'MULTITOOL_VERSION' ) ) {
            define( 'MULTITOOL_VERSION', '1.2.1' );
        }
        if ( ! defined( 'MULTITOOL_DIR_PATH' ) ) {
            define( 'MULTITOOL_DIR_PATH', MULTITOOL_PLUGIN_DIR_PATH );
        }
    }

    /**
     * Load the modern runtime layer.
     */
    private static function load_modern_runtime() {
        require_once MULTITOOL_DIR_PATH . 'src/ServiceContainer.php';
        require_once MULTITOOL_DIR_PATH . 'src/Services/ToolRegistry.php';
        require_once MULTITOOL_DIR_PATH . 'src/Services/ToolDiagnostics.php';
        require_once MULTITOOL_DIR_PATH . 'src/Admin/ToolsPage.php';
    }

    /**
     * Load the legacy runtime layer.
     */
    private static function load_legacy_runtime() {
        // Legacy files will be loaded as before
    }

    /**
     * Register plugin hooks.
     */
    private static function register_hooks() {
        // Initialize tool registry on plugins_loaded
        add_action( 'plugins_loaded', array( __CLASS__, 'init_tool_registry' ), 5 );
        
        // Load plugin textdomain
        add_action( 'plugins_loaded', array( __CLASS__, 'load_textdomain' ) );
    }

    /**
     * Initialize the tool registry.
     */
    public static function init_tool_registry() {
        $container = \Multitool\ServiceContainer::instance();
        $registry = $container->tool_registry();
        $registry->discover_tools();
    }

    /**
     * Load plugin textdomain.
     */
    public static function load_textdomain() {
        load_plugin_textdomain(
            'multitool',
            false,
            dirname( MULTITOOL_PLUGIN_BASENAME ) . '/i18n/languages/'
        );
    }
}
