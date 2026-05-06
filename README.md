# Multitool

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)

> The Swiss-Army-Plugin for WordPress. A collection of small utilities and quick-fix tools for site administrators.

---

## Features

- **Quick Tools** — One-click solutions for common WordPress administration tasks
- **Configuration Tools** — Tools with configurable options for more complex operations
- **Extension System** — Extendable architecture for adding custom tools
- **Installation Wizard** — Guided setup on first activation
- **Maintenance Mode** — Display "Under Construction" to visitors with one click
- **Display Errors Toggle** — Quick debugging tool for development
- **Security Tools** — Detect unauthorized administrator accounts

---

## Installation

1. Upload the `multitool` folder to `wp-content/plugins/`
2. Activate the plugin through the WordPress Plugins menu
3. Follow the installation wizard
4. Access tools via the Multitool admin menu

---

## Requirements

- WordPress 5.0+
- PHP 7.4+

---

## Creating Extensions

Multitool supports extensions — create your own tools without building a full plugin:

```php
// Register your extension
add_filter('multitool_extensions', function($extensions) {
    $extensions['my-tool'] = array(
        'name' => 'My Custom Tool',
        'callback' => 'my_tool_render',
    );
    return $extensions;
});
```

---

## Changelog

See [readme.txt](readme.txt) for full changelog.

---

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## License

GPL v3 — see [license.txt](license.txt) for details.

---

## Author

**Ryan Bayne** — [GitHub](https://github.com/RyanBayne) · [LinkedIn](https://www.linkedin.com/in/ryanrbayne/)
