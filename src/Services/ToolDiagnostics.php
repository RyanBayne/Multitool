<?php
/**
 * Tool Diagnostics Service
 * 
 * Provides health inspection and tool capability assessment.
 *
 * @package Multitool\Services
 */

namespace Multitool\Services;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ToolDiagnostics {

    /**
     * @var ToolRegistry
     */
    private $registry;

    /**
     * Constructor.
     *
     * @param ToolRegistry $registry
     */
    public function __construct( ToolRegistry $registry ) {
        $this->registry = $registry;
    }

    /**
     * Get full plugin health report.
     *
     * @return array
     */
    public function get_health_report() {
        return array(
            'status' => 'healthy',
            'plugin_active' => is_plugin_active( plugin_basename( MULTITOOL_PLUGIN_FILE ) ),
            'wp_version' => get_bloginfo( 'version' ),
            'php_version' => phpversion(),
            'tools_registered' => $this->registry->get_stats()['total_registered'],
            'user_available' => $this->registry->get_stats()['user_available'],
            'issues' => $this->detect_issues(),
        );
    }

    /**
     * Detect potential issues.
     *
     * @return array
     */
    public function detect_issues() {
        $issues = array();

        // Check PHP version
        if ( version_compare( phpversion(), '7.4.0', '<' ) ) {
            $issues[] = array(
                'severity' => 'warning',
                'message' => 'PHP version below 7.4.0 - some tools may not work',
                'code' => 'php_version_low',
            );
        }

        // Check for tool registration failures
        if ( $this->registry->get_stats()['total_registered'] === 0 ) {
            $issues[] = array(
                'severity' => 'warning',
                'message' => 'No tools registered - check hook registration',
                'code' => 'no_tools_registered',
            );
        }

        return $issues;
    }

    /**
     * Get tool inventory with detailed status.
     *
     * @return array
     */
    public function get_tool_inventory() {
        $tools = $this->registry->get_all_tools();
        $inventory = array();

        foreach ( $tools as $tool_id => $tool ) {
            $is_available = $this->registry->has_tool( $tool_id );

            $inventory[] = array(
                'id' => $tool_id,
                'name' => $tool['name'],
                'description' => $tool['description'],
                'category' => $tool['category'],
                'capability' => $tool['capability'],
                'is_safe' => $tool['is_safe'],
                'requires_confirmation' => $tool['requires_confirmation'],
                'is_available' => $is_available,
                'user_access' => $is_available ? 'granted' : 'denied',
                'callback_valid' => is_callable( $tool['callback'] ),
            );
        }

        return $inventory;
    }

    /**
     * Get capability assessment for current user.
     *
     * @return array
     */
    public function get_user_capability_assessment() {
        $current_user = wp_get_current_user();

        if ( ! $current_user->exists() ) {
            return array(
                'user_id' => 0,
                'user_name' => 'Guest',
                'total_tools_available' => 0,
                'tools_available' => array(),
                'capabilities' => array(),
            );
        }

        $available_tools = $this->registry->get_available_tools();
        $tool_ids = array_keys( $available_tools );

        return array(
            'user_id' => $current_user->ID,
            'user_name' => $current_user->display_name,
            'total_tools_available' => count( $available_tools ),
            'tools_available' => $tool_ids,
            'capabilities' => $current_user->get_role_caps(),
        );
    }

    /**
     * Get tool category breakdown.
     *
     * @return array
     */
    public function get_category_breakdown() {
        $categories = $this->registry->get_categories();
        $breakdown = array();

        foreach ( $categories as $category ) {
            $category_tools = $this->registry->get_tools_by_category( $category );

            $breakdown[ $category ] = array(
                'total' => count( $category_tools ),
                'safe' => count( array_filter( $category_tools, function( $t ) {
                    return $t['is_safe'];
                } ) ),
                'risky' => count( array_filter( $category_tools, function( $t ) {
                    return ! $t['is_safe'];
                } ) ),
                'tools' => array_keys( $category_tools ),
            );
        }

        return $breakdown;
    }

    /**
     * Get full diagnostic report.
     *
     * @return array
     */
    public function get_full_diagnostic() {
        return array(
            'timestamp' => current_time( 'mysql' ),
            'health' => $this->get_health_report(),
            'statistics' => $this->registry->get_stats(),
            'tool_inventory' => $this->get_tool_inventory(),
            'user_assessment' => $this->get_user_capability_assessment(),
            'category_breakdown' => $this->get_category_breakdown(),
        );
    }

    /**
     * Check if tool system is ready.
     *
     * @return bool
     */
    public function is_system_ready() {
        $issues = $this->detect_issues();

        // System is ready if no critical issues
        foreach ( $issues as $issue ) {
            if ( 'error' === $issue['severity'] ) {
                return false;
            }
        }

        return true;
    }
}
