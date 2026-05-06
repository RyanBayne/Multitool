# Multitool Implementation Baseline

## Current Modern Structure

The modern Multitool plugin has been restructured to support a **registry-based tool platform** with explicit tool discovery, capability declaration, and admin management.

### Modern Bootstrap & Service Layer

**bootstrap.php** - Entry point establishing modern initialization pattern
- Defines plugin constants (MULTITOOL_PLUGIN_FILE, MULTITOOL_VERSION, etc.)
- Loads modern service layer (ServiceContainer, ToolRegistry, ToolDiagnostics, ToolsPage)
- Maintains backward compatibility with legacy classes
- Registers hooks for tool discovery on plugins_loaded

**src/ServiceContainer.php** - Central service registry
- Singleton pattern with lazy-loaded services
- Methods:
  - `tool_registry()` - Returns ToolRegistry instance
  - `tool_diagnostics()` - Returns ToolDiagnostics with registry dependency
  - `tools_page()` - Returns ToolsPage admin interface

**src/Services/ToolRegistry.php** - Tool platform core
- Tool registration: `register_tool($tool_id, $tool_config)` with capability, category, callback
- Tool discovery: `discover_tools()` fires `multitool_register_tools` action for plugins to register
- Tool query: `get_all_tools()`, `get_tools_by_category()`, `get_available_tools()` (capability-aware)
- Tool execution: `execute_tool($tool_id, $args)` with safety/confirmation checks
- Tool statistics: `get_stats()` returns registered/available/safe/risky counts
- Hooks: `multitool_tool_registered`, `multitool_register_tools`, `multitool_before_execute`, `multitool_after_execute`

**src/Services/ToolDiagnostics.php** - Health & capability assessment
- `get_health_report()` - Plugin status, version checks, issue detection
- `get_tool_inventory()` - Detailed tool list with availability, callback validation
- `get_user_capability_assessment()` - User-specific available tools and capabilities
- `get_category_breakdown()` - Tools grouped by category with safety statistics
- `get_full_diagnostic()` - Comprehensive report (health, stats, inventory, user, categories)
- `is_system_ready()` - Boolean check for critical issues

**src/Admin/ToolsPage.php** - WordPress admin interface
- Dashboard page showing system health, stats, and quick links
- Tool inventory page with category breakdown and status table
- Diagnostics page showing user capabilities and system statistics
- Hooks: `admin_menu`, `admin_enqueue_scripts`
- Admin slugs: `multitool`, `multitool-tools`, `multitool-diagnostics`

## Data Model

### Tool Configuration Object
```php
[
    'id' => 'tool_id',                          // Unique identifier
    'name' => 'Display Name',                   // Human-readable name
    'description' => 'What it does',            // Capability description
    'capability' => 'manage_options',           // WordPress capability requirement
    'category' => 'general',                    // Tool category for organization
    'callback' => callable,                     // Execution function/method
    'priority' => 10,                           // Display/execution priority
    'is_safe' => true,                          // Safety flag (default true)
    'requires_confirmation' => false,           // Confirmation requirement
]
```

### Tool Status Reporting
- Available: Tool registered AND user has capability
- Unavailable: Tool registered BUT user lacks capability
- Safe/Risky: Flags for admin awareness and execution policies
- Callback Valid: Verified callable before execution attempt

## Current Features

✓ Registry-based tool registration system
✓ Capability-aware tool availability (respects WordPress roles/capabilities)
✓ Tool category organization
✓ Built-in health diagnostics
✓ User capability assessment
✓ Admin dashboard with tool inventory and status
✓ Safety flags and confirmation requirements
✓ Hook system for external tool registration (`multitool_register_tools`)
✓ Pre/post execution hooks for tool lifecycle
✓ Exception handling in tool execution
✓ Tool statistics (total, safe, risky, per-category)

## Rendering Surfaces

### Admin Interface
- Tools main menu (`admin.php?page=multitool`)
- Tool inventory submenu (`admin.php?page=multitool-tools`)
- Diagnostics submenu (`admin.php?page=multitool-diagnostics`)

### Programmatic Access
- `\Multitool\ServiceContainer::instance()->tool_registry()` - Full registry access
- `\Multitool\ServiceContainer::instance()->tool_diagnostics()` - Health/capability reports
- WordPress hooks: Register tools via `multitool_register_tools` action

## Architectural Decisions

**Why Registry Pattern?**
- Multitool's core purpose is to provide a "Swiss Army" of tools
- Registry enables external plugins/themes to register tools dynamically
- Explicit registration creates safety boundaries and audit trail

**Why Capability-Aware?**
- Multitool tools may be destructive (reset, rebuild, export, etc.)
- WordPress capabilities provide familiar permission model
- `requires_confirmation` flag for extra safety on risky tools

**Why Singleton ServiceContainer?**
- Bootstrap pattern reduces entry file complexity
- Single consistent access point for all services
- Lazy-loading services only when needed
- Compatible with legacy class instantiation patterns

**Why Diagnostics Service?**
- Tools can fail silently if dependencies missing
- System health visibility for administrators
- Capability assessment helps with "why can't I see this tool?" questions
- Category breakdown helps organize large tool collections

**Why Preserve Legacy Classes?**
- Multitool has existing code and user scripts that depend on it
- Allows phased migration to modern patterns
- Legacy WordPress hooks still function (plugins_loaded, init, etc.)

## Next Steps (Future)

- **Tool Execute UI**: Add admin page to safely execute tools with forms
- **Tool History**: Track which user executed which tools and when
- **REST API**: Expose tool registry and execution via /wp-json/multitool/
- **Import/Export**: Tool configuration backup and restore
- **Custom Permissions**: Per-tool capability override system
- **Tool Marketplace**: Catalog of available tools to download/register
- **Scheduled Tools**: Allow tools to run on cron schedule
- **Tool Dependencies**: Declare tool prerequisites and auto-enable
- **Activity Log**: Integration with activity logging plugins
- **Role Editor**: UI for custom WordPress roles with tool-specific capabilities

## Architecture Diagram

```
multitool.php (Entry)
    ↓
bootstrap.php (Load modern layer)
    ├→ ServiceContainer (Singleton Registry)
    │   ├→ ToolRegistry
    │   ├→ ToolDiagnostics
    │   └→ ToolsPage
    │
    ├→ Register Hooks
    │   └→ plugins_loaded: init_tool_registry()
    │       └→ ToolRegistry::discover_tools()
    │           └→ do_action('multitool_register_tools', $registry)
    │
    └→ Load Legacy Classes (WordPressMultitool, etc.)
        └→ Maintain backward compatibility
```

## Files & Load Order

1. `multitool.php` - Minimal entry file (delegates to bootstrap)
2. `bootstrap.php` - Modern initialization pattern
3. `src/ServiceContainer.php` - Service registry
4. `src/Services/ToolRegistry.php` - Tool registration & discovery
5. `src/Services/ToolDiagnostics.php` - Health & capability checks
6. `src/Admin/ToolsPage.php` - WordPress admin pages
7. Legacy classes (unchanged, backward compatible)

## Migration Path

The modern layer coexists with legacy code:
- New features use ServiceContainer and modern patterns
- Existing code continues to work as-is
- Can gradually adopt modern patterns piece-by-piece
- No breaking changes to user-facing functionality
