<?php
/**
 * Tools Admin Page
 * 
 * Provides WordPress admin interface for viewing and managing tools.
 *
 * @package Multitool\Admin
 */

namespace Multitool\Admin;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ToolsPage {

    /**
     * @var \Multitool\Services\ToolDiagnostics
     */
    private $diagnostics;

    /**
     * Constructor.
     *
     * @param \Multitool\Services\ToolDiagnostics $diagnostics
     */
    public function __construct( \Multitool\Services\ToolDiagnostics $diagnostics ) {
        $this->diagnostics = $diagnostics;
    }

    /**
     * Register the admin page.
     */
    public function register() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
    }

    /**
     * Add admin menu.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Multitool', 'multitool' ),
            __( 'Multitool', 'multitool' ),
            'manage_options',
            'multitool',
            array( $this, 'render_page' ),
            'dashicons-admin-tools'
        );

        add_submenu_page(
            'multitool',
            __( 'Tools', 'multitool' ),
            __( 'Tools', 'multitool' ),
            'manage_options',
            'multitool-tools',
            array( $this, 'render_tools_page' )
        );

        add_submenu_page(
            'multitool',
            __( 'Diagnostics', 'multitool' ),
            __( 'Diagnostics', 'multitool' ),
            'manage_options',
            'multitool-diagnostics',
            array( $this, 'render_diagnostics_page' )
        );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @param string $hook_suffix
     */
    public function enqueue_scripts( $hook_suffix ) {
        if ( false === strpos( $hook_suffix, 'multitool' ) ) {
            return;
        }

        wp_enqueue_style(
            'multitool-admin',
            plugins_url( 'assets/css/admin.css', MULTITOOL_PLUGIN_FILE ),
            array(),
            MULTITOOL_VERSION
        );

        wp_enqueue_script(
            'multitool-admin',
            plugins_url( 'assets/js/admin.js', MULTITOOL_PLUGIN_FILE ),
            array( 'jquery' ),
            MULTITOOL_VERSION,
            true
        );
    }

    /**
     * Render main dashboard page.
     */
    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized', 'multitool' ) );
        }

        $health = $this->diagnostics->get_health_report();
        $stats = $this->diagnostics->get_full_diagnostic()['statistics'];
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Multitool Dashboard', 'multitool' ); ?></h1>
            
            <div class="multitool-dashboard">
                <div class="multitool-status-card">
                    <h3><?php esc_html_e( 'System Status', 'multitool' ); ?></h3>
                    <p>
                        <strong><?php esc_html_e( 'Status:', 'multitool' ); ?></strong> 
                        <span class="status-badge status-<?php echo esc_attr( $health['status'] ); ?>">
                            <?php echo esc_html( ucfirst( $health['status'] ) ); ?>
                        </span>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Tools Registered:', 'multitool' ); ?></strong> 
                        <?php echo intval( $stats['total_registered'] ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Tools Available:', 'multitool' ); ?></strong> 
                        <?php echo intval( $stats['user_available'] ); ?>
                    </p>
                </div>

                <div class="multitool-status-card">
                    <h3><?php esc_html_e( 'Environment', 'multitool' ); ?></h3>
                    <p>
                        <strong><?php esc_html_e( 'WordPress:', 'multitool' ); ?></strong> 
                        <?php echo esc_html( $health['wp_version'] ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'PHP:', 'multitool' ); ?></strong> 
                        <?php echo esc_html( $health['php_version'] ); ?>
                    </p>
                    <p>
                        <strong><?php esc_html_e( 'Plugin Active:', 'multitool' ); ?></strong> 
                        <?php echo $health['plugin_active'] ? '✓' : '✗'; ?>
                    </p>
                </div>

                <?php if ( ! empty( $health['issues'] ) ) : ?>
                    <div class="multitool-status-card multitool-issues">
                        <h3><?php esc_html_e( 'Issues Detected', 'multitool' ); ?></h3>
                        <ul>
                            <?php foreach ( $health['issues'] as $issue ) : ?>
                                <li class="issue-<?php echo esc_attr( $issue['severity'] ); ?>">
                                    <?php echo esc_html( $issue['message'] ); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <div class="multitool-quick-links">
                <h2><?php esc_html_e( 'Quick Links', 'multitool' ); ?></h2>
                <ul>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=multitool-tools' ) ); ?>">
                            <?php esc_html_e( 'View All Tools', 'multitool' ); ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=multitool-diagnostics' ) ); ?>">
                            <?php esc_html_e( 'Detailed Diagnostics', 'multitool' ); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php
    }

    /**
     * Render tools inventory page.
     */
    public function render_tools_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized', 'multitool' ) );
        }

        $inventory = $this->diagnostics->get_tool_inventory();
        $breakdown = $this->diagnostics->get_category_breakdown();
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Tool Inventory', 'multitool' ); ?></h1>

            <?php if ( empty( $inventory ) ) : ?>
                <div class="notice notice-info">
                    <p><?php esc_html_e( 'No tools currently registered.', 'multitool' ); ?></p>
                </div>
            <?php else : ?>
                <div class="multitool-category-stats">
                    <?php foreach ( $breakdown as $category => $stats ) : ?>
                        <div class="category-stat">
                            <h4><?php echo esc_html( ucfirst( $category ) ); ?></h4>
                            <p><?php echo intval( $stats['total'] ); ?> <?php esc_html_e( 'tools', 'multitool' ); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <table class="wp-list-table widefat striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Tool Name', 'multitool' ); ?></th>
                            <th><?php esc_html_e( 'Category', 'multitool' ); ?></th>
                            <th><?php esc_html_e( 'Description', 'multitool' ); ?></th>
                            <th><?php esc_html_e( 'Status', 'multitool' ); ?></th>
                            <th><?php esc_html_e( 'Safety', 'multitool' ); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $inventory as $tool ) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $tool['name'] ); ?></strong><br>
                                    <small><?php echo esc_html( $tool['id'] ); ?></small>
                                </td>
                                <td><?php echo esc_html( ucfirst( $tool['category'] ) ); ?></td>
                                <td><?php echo esc_html( $tool['description'] ); ?></td>
                                <td>
                                    <?php if ( $tool['is_available'] ) : ?>
                                        <span class="status-badge status-available">✓ Available</span>
                                    <?php else : ?>
                                        <span class="status-badge status-unavailable">✗ Not Available</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ( $tool['is_safe'] ) : ?>
                                        <span class="safety-badge safety-safe">Safe</span>
                                    <?php else : ?>
                                        <span class="safety-badge safety-risky">Risky</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Render detailed diagnostics page.
     */
    public function render_diagnostics_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'Unauthorized', 'multitool' ) );
        }

        $diagnostic = $this->diagnostics->get_full_diagnostic();
        $user_assessment = $diagnostic['user_assessment'];
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Multitool Diagnostics', 'multitool' ); ?></h1>

            <div class="multitool-diagnostic-report">
                <h2><?php esc_html_e( 'User Capabilities', 'multitool' ); ?></h2>
                <p>
                    <strong><?php esc_html_e( 'User:', 'multitool' ); ?></strong> 
                    <?php echo esc_html( $user_assessment['user_name'] ); ?> 
                    (ID: <?php echo intval( $user_assessment['user_id'] ); ?>)
                </p>
                <p>
                    <strong><?php esc_html_e( 'Tools Available:', 'multitool' ); ?></strong> 
                    <?php echo intval( $user_assessment['total_tools_available'] ); ?>
                </p>
                
                <?php if ( ! empty( $user_assessment['tools_available'] ) ) : ?>
                    <h3><?php esc_html_e( 'Accessible Tools', 'multitool' ); ?></h3>
                    <ul>
                        <?php foreach ( $user_assessment['tools_available'] as $tool_id ) : ?>
                            <li><?php echo esc_html( $tool_id ); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <h2><?php esc_html_e( 'System Statistics', 'multitool' ); ?></h2>
                <ul>
                    <li><?php echo sprintf( esc_html__( 'Total Tools Registered: %d', 'multitool' ), intval( $diagnostic['statistics']['total_registered'] ) ); ?></li>
                    <li><?php echo sprintf( esc_html__( 'Safe Tools: %d', 'multitool' ), intval( $diagnostic['statistics']['safe_tools'] ) ); ?></li>
                    <li><?php echo sprintf( esc_html__( 'Risky Tools: %d', 'multitool' ), intval( $diagnostic['statistics']['risky_tools'] ) ); ?></li>
                </ul>

                <div class="notice notice-info inline">
                    <p><?php echo sprintf( esc_html__( 'Report Generated: %s', 'multitool' ), esc_html( $diagnostic['timestamp'] ) ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}
