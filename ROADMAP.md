# Multitool Roadmap

## Implementation Status

### Phase 1: Bootstrap & Registry Foundation ✓ COMPLETE

- **1.1 Bootstrap & Namespace Integration** ✓ 
  - Added `bootstrap.php` with modern initialization pattern
  - Namespaced service layer: `Multitool\Bootstrap`, `Multitool\ServiceContainer`, `Multitool\Services\*`, `Multitool\Admin\*`
  - Constants defined: MULTITOOL_PLUGIN_FILE, MULTITOOL_VERSION, MULTITOOL_DIR_PATH
  - Backward compatible with existing legacy classes

- **1.2 Registry Design & Implementation** ✓
  - Tool Registry contract defined in `src/Services/ToolRegistry.php`
  - Each tool declares: id, name, description, category, capability, callback, is_safe, requires_confirmation
  - Registry supports: `register_tool()`, `get_all_tools()`, `get_available_tools()` (capability-aware), `execute_tool()` with safety checks
  - Discovery hook: `multitool_register_tools` action for external tools
  - Tool statistics: `get_stats()` returns total, available, safe, risky, per-category counts

- **1.3 Diagnostics & Admin Interface** ✓
  - Diagnostics service in `src/Services/ToolDiagnostics.php`
  - Health report, tool inventory, user capability assessment, category breakdown
  - Admin pages in `src/Admin/ToolsPage.php`: dashboard, tool inventory, detailed diagnostics
  - Admin menu registration with appropriate capabilities

**Documentation:** See [IMPLEMENTATION-BASELINE.md](IMPLEMENTATION-BASELINE.md) for architecture details.

---

## Purpose

Multitool has the broadest scope and the highest risk of drifting into an unstructured utility dump. Its future should be a modular WordPress utility platform with explicit tool registration, conditional availability, and strong safety boundaries.

This roadmap prioritizes standards first, then architecture that can support a growing library of tools without repeating the weaknesses of the legacy system.

## Current State

### Strengths

- The plugin already has the right broad product concept: small practical tools in one place.
- An extension/modular idea already exists.
- There are examples of quick tools, configuration tools, and request listeners.
- Background-processing libraries are already present and could inform future safe execution patterns.

### Key Evidence In Current Code

- Bootstrap and loader: `multitool.php`
- Install/update flow: `includes/class.multitool-install.php`
- Option model: `includes/class.multitool-options.php`
- AJAX endpoint layer: `includes/class.multitool-ajax.php`
- Listener model: `includes/class.multitool-listener.php`
- Tool examples: `includes/class.multitool-configurationtools.php`
- Maintenance example: `includes/tools/class.multitool-maintenance-tool.php`
- Background processing libraries: `includes/libraries/library.async-request.php`, `includes/libraries/library.background-process.php`

### Standards Gaps

- No namespaces or modern autoloading.
- Global singleton and helper patterns are still strongly legacy.
- Tool discovery/execution is implicit and switch-driven rather than explicit and inspectable.
- Some legacy debug and execution patterns are too risky to carry forward unchanged.
- Admin structure is older and not easily extensible.
- Current option model is serviceable but needs a clearer contract for future tools.
- Documentation and tests do not match modern standards.

## Product Direction

Multitool should become a registry-based utility platform for WordPress administrators and developers.

That means:

- each tool is explicitly registered,
- each tool declares its capabilities and safety requirements,
- each tool can declare when it is applicable,
- theme/plugin-specific tools appear only when relevant,
- risky tools are gated or reworked rather than hidden in listeners.

## Non-Negotiable Legacy Behaviors To Preserve

- Existing useful tools should remain available where safe.
- The idea of quick tools versus configurable tools can remain, but on a new internal architecture.
- The extension concept should survive, but with a formal registration contract.
- Existing options should remain readable during migration.

## Modernization Principles

- Introduce namespaces and a predictable loader.
- Replace implicit listeners with explicit tool registration and execution flows.
- Require capability checks, nonces, and execution boundaries for all tools.
- Prefer read-only diagnostics before destructive actions.
- Use conditional availability so tools only appear when relevant to the current environment.
- Treat WPSeed standards as the baseline for docs, install/uninstall, and admin separation.

## Phase 1: Tool Inventory And Classification

### 1.1 Inventory Existing Tools

- List every current tool, listener-driven action, and extension surface.
- Classify each as:
  - core,
  - optional,
  - deprecated,
  - unsafe,
  - extension candidate.

### 1.2 Identify Unsafe Patterns

- Review display-errors and similar tools for unsafe behavior.
- Review any tool that mutates roles, accounts, or frontend output automatically.
- Define which tools need redesign before they can stay in the product.

### 1.3 Registry Design

- Define a Tool Registry contract.
- Each tool should declare:
  - slug,
  - label,
  - description,
  - category,
  - required capability,
  - execution mode,
  - applicability rules,
  - risk level.

## Phase 2: Architecture Rebuild

### 2.1 Namespace And Loader Plan

- Add namespaces for new code.
- Introduce registry and service classes without breaking the existing bootstrap immediately.
- Add compatibility wrappers for legacy tools where needed.

### 2.2 Admin IA Redesign

- Replace ad hoc tool discovery with categorized tool screens.
- Separate diagnostics, maintenance, security, content, developer, and ecosystem-aware tools.
- Show tool applicability and safety notes in the UI.

### 2.3 Execution Boundaries

- Support dry-run mode for eligible tools.
- Require explicit confirmation for destructive actions.
- Add result reporting and logging per tool run.

## Phase 3: Conditional Tooling

### 3.1 Environment-Aware Visibility

- Only show relevant tools when a dependent theme or plugin is active.
- Detect common ecosystems and register related tools conditionally.

### 3.2 Extension Model Refresh

- Redesign the extension system around the new registry.
- Make third-party tool registration explicit and documented.
- Prevent extension code from bypassing capability and safety rules.

## Phase 4: Tool Set Priorities

### 4.1 Keep And Rebuild First

- Site diagnostics.
- Maintenance mode.
- Safe content cleanup helpers.
- Settings import/export helpers.
- Plugin/theme detection utilities.
- Reporting and inspection tools.

### 4.2 Rework Before Keeping

- Debug/display-error tools.
- Tools that change user roles automatically.
- Any tool that runs passively from listeners without clear user intent.

### 4.3 Expand Later

- Theme/plugin-specific repair tools.
- Site health augmentation.
- Developer-oriented inspection tools.
- Database reporting and migration helpers.
- Ecosystem-aware utilities for other plugins in the product family.

## Phase 5: Standards And Safety Refresh

- Add capability checks and nonces consistently.
- Review options and AJAX endpoints for clearer validation rules.
- Modernize uninstall cleanup.
- Align admin assets and page registration with current standards.
- Add documentation for tool authors and site administrators.

## Phase 6: Feature Opportunities

- Tool packs by category or ecosystem.
- Read-only diagnostics mode for client environments.
- Exportable reports for audits and support tickets.
- WP-CLI companions for selected tools.
- Scheduled safe maintenance jobs backed by explicit user configuration.
- Marketplace-style extension discovery in a later phase.

## First Implementation Slice

1. Inventory all current tools and listener-driven behaviors.
2. Classify them as core, optional, deprecated, or unsafe.
3. Build the Tool Registry contract.
4. Port one safe tool, such as maintenance mode, through the registry model before moving broader surfaces.

This is the correct starting point because it prevents future feature drift and gives the plugin a structure strong enough to support growth.

## Risks

- The current tool/listener behavior may hide side effects that users depend on.
- A broad plugin can become unfocused unless every new tool meets registry and applicability rules.
- Some legacy tools may not be safe enough to preserve exactly as-is.
- Extension support can create instability if contracts are not formalized early.

## Verification Expectations

- Existing useful tools remain reachable during migration.
- Tool visibility respects capabilities and applicability rules.
- Destructive tools require explicit intent and confirmation.
- Tool execution results are logged or surfaced clearly.
- The first registry-ported tools behave the same or better than legacy equivalents.