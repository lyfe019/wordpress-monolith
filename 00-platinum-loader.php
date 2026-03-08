<?php
/**
 * Platinum Edge Master Loader
 * Path: /load-platinum.php
 */

defined('ABSPATH') || exit;

// 1. Setup Autoloader - CRITICAL: This is the heart of the system now!
require_once __DIR__ . '/platinum-edge-core/vendor/autoload.php';

require_once __DIR__ . '/platinum-edge-core/platinum-edge-core.php';

/**
 * Bootstraps the Platinum Kernel.
 */
function boot_platinum() {
    try {
        \Platinum\Core\Kernel::boot();
        \Platinum\Core\App::boot(); // New namespace is correct!
    } catch (\Throwable $e) {
        error_log('[Platinum Initializer] Boot Failed: ' . $e->getMessage());
        if (defined('WP_DEBUG') && WP_DEBUG) {
            throw $e;
        }
    }
}

// Auto-boot if we are in a WordPress Web Request
if (!defined('PLATINUM_MANUAL_BOOT')) {
    add_action('plugins_loaded', 'boot_platinum', 10);
}