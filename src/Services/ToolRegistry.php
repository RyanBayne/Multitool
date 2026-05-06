<?php
/**
 * Tool Registry Service
 * 
 * Manages tool discovery, registration, and availability logic.
 * Provides the explicit tool registry system described in the Multitool roadmap.
 *
 * @package Multitool\Services
 */

namespace Multitool\Services;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ToolRegistry {

    /**
     * @var array Registered tools
     */
    private $tools = array();

    /**
     * @var bool Whether tools have been discovered
     */
    private $discovered = false;

    /**
     * Register a tool.
     *
     * @param string $tool_id Unique tool identifier
     * @param array $tool_config Tool configuration array
     * @return bool
     */
    public function register_tool( $tool_id, $tool_config ) {
        if ( isset( $this->tools[ $tool_id ] ) ) {
            return false; // Tool already registered
        }

        $tool = array(
            'id' => $tool_id,
            'name' => $tool_config['name'] ?? 'Untitled Tool',
            'description' => $tool_config['description'] ?? '',
            'capability' => $tool_config['capability'] ?? 'manage_options',
            'category' => $tool_config['category'] ?? 'general',
            'callback' => $tool_config['callback'] ?? null,
            'priority' => $tool_config['priority'] ?? 10,
            'is_safe' => $tool_config['is_safe'] ?? true,
            'requires_confirmation' => $tool_config['requires_confirmation'] ?? false,
        );

        $this->tools[ $tool_id ] = $tool;

        /**
         * Fired when a tool is registered.
         *
         * @param string $tool_id
         * @param array $tool
         */
        do_action( 'multitool_tool_registered', $tool_id, $tool );

        return true;
    }

    /**
     * Get a registered tool by ID.
     *
     * @param string $tool_id
     * @return array|null
     */
    public function get_tool( $tool_id ) {
        return $this->tools[ $tool_id ] ?? null;
    }

    /**
     * Get all registered tools.
     *
     * @return array
     */
    public function get_all_tools() {
        return $this->tools;
    }

    /**
     * Get tools by category.
     *
     * @param string $category
     * @return array
     */
    public function get_tools_by_category( $category ) {
        return array_filter( $this->tools, function( $tool ) use ( $category ) {
            return $tool['category'] === $category;
        } );
    }

    /**
     * Get available tools for the current user.
     *
     * @return array
     */
    public function get_available_tools() {
        $available = array();

        foreach ( $this->tools as $tool_id => $tool ) {
            if ( current_user_can( $tool['capability'] ) ) {
                $available[ $tool_id ] = $tool;
            }
        }

        return $available;
    }

    /**
     * Check if a tool exists and is available.
     *
     * @param string $tool_id
     * @return bool
     */
    public function has_tool( $tool_id ) {
        if ( ! isset( $this->tools[ $tool_id ] ) ) {
            return false;
        }

        $tool = $this->tools[ $tool_id ];
        return current_user_can( $tool['capability'] );
    }

    /**
     * Execute a tool by ID.
     *
     * @param string $tool_id
     * @param array $args Optional arguments to pass to the tool
     * @return mixed Tool execution result
     */
    public function execute_tool( $tool_id, $args = array() ) {
        if ( ! $this->has_tool( $tool_id ) ) {
            return new \WP_Error( 'tool_not_found', 'Tool not found or not available' );
        }

        $tool = $this->tools[ $tool_id ];

        if ( ! is_callable( $tool['callback'] ) ) {
            return new \WP_Error( 'invalid_callback', 'Tool callback is not callable' );
        }

        // Check for safety confirmations
        if ( $tool['requires_confirmation'] && ! isset( $args['confirmed'] ) ) {
            return new \WP_Error( 'confirmation_required', 'Tool execution requires confirmation' );
        }

        /**
         * Fire before tool execution hook.
         *
         * @param string $tool_id
         * @param array $args
         */
        do_action( 'multitool_before_execute', $tool_id, $args );

        try {
            $result = call_user_func( $tool['callback'], $args );
        } catch ( \Exception $e ) {
            return new \WP_Error( 'execution_error', $e->getMessage() );
        }

        /**
         * Fire after tool execution hook.
         *
         * @param string $tool_id
         * @param mixed $result
         * @param array $args
         */
        do_action( 'multitool_after_execute', $tool_id, $result, $args );

        return $result;
    }

    /**
     * Discover and register built-in tools.
     */
    public function discover_tools() {
        if ( $this->discovered ) {
            return; // Already discovered
        }

        /**
         * Allow plugins/themes to register their tools.
         *
         * @param \Multitool\Services\ToolRegistry $registry
         */
        do_action( 'multitool_register_tools', $this );

        $this->discovered = true;
    }

    /**
     * Get tool categories.
     *
     * @return array
     */
    public function get_categories() {
        $categories = array();

        foreach ( $this->tools as $tool ) {
            $categories[ $tool['category'] ] = true;
        }

        return array_keys( $categories );
    }

    /**
     * Get tool statistics.
     *
     * @return array
     */
    public function get_stats() {
        return array(
            'total_registered' => count( $this->tools ),
            'user_available' => count( $this->get_available_tools() ),
            'categories' => $this->get_categories(),
            'safe_tools' => count( array_filter( $this->tools, function( $t ) {
                return $t['is_safe'];
            } ) ),
            'risky_tools' => count( array_filter( $this->tools, function( $t ) {
                return ! $t['is_safe'];
            } ) ),
        );
    }
}
