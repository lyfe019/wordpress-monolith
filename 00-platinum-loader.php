<?php
/**
 * Platinum Edge Master Loader
 * Path: /wp-content/mu-plugins/00-platinum-loader.php
 */

defined('ABSPATH') || exit;

// 1. Initialize the Autoloader (The System's Nervous System)
$autoloader = __DIR__ . '/platinum-edge-core/vendor/autoload.php';

if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    error_log('[Platinum Error] Autoloader not found. Run composer install.');
    return;
}

// 2. Load the Core Entry Point
require_once __DIR__ . '/platinum-edge-core/platinum-edge-core.php';

/**
 * Bootstraps the Platinum Kernel and App.
 * Wrapped in a function so it can be called manually in tests.
 */
function boot_platinum() {
    try {
        \Platinum\Core\Kernel::boot();
        \Platinum\Core\App::boot();
    } catch (\Throwable $e) {
        error_log('[Platinum Boot Failed] ' . $e->getMessage());
    }
}

// 3. Deterministic Booting
// We wait until plugins are loaded to ensure WP environment is stable,
// but before the rest of the site processes.
if (!defined('PLATINUM_MANUAL_BOOT')) {
    add_action('plugins_loaded', 'boot_platinum', 1);
}