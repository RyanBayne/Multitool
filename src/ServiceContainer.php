<?php
/**
 * Multitool Service Container
 * 
 * Central registry for modern Multitool services.
 *
 * @package Multitool
 */

namespace Multitool;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ServiceContainer {

    private static $instance = null;

    private $services = array();

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function tool_registry() {
        if ( ! isset( $this->services['tool_registry'] ) ) {
            $this->services['tool_registry'] = new Services\ToolRegistry();
        }

        return $this->services['tool_registry'];
    }

    public function tool_diagnostics() {
        if ( ! isset( $this->services['tool_diagnostics'] ) ) {
            $this->services['tool_diagnostics'] = new Services\ToolDiagnostics(
                $this->tool_registry()
            );
        }

        return $this->services['tool_diagnostics'];
    }

    public function tools_page() {
        if ( ! isset( $this->services['tools_page'] ) ) {
            $this->services['tools_page'] = new Admin\ToolsPage(
                $this->tool_diagnostics()
            );
        }

        return $this->services['tools_page'];
    }
}
