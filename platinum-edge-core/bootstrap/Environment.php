<?php
namespace Platinum\Core;

final class Environment
{
    public static function type(): string
    {
        return defined('WP_ENVIRONMENT_TYPE') ? WP_ENVIRONMENT_TYPE : 'production';
    }

    public static function isDebug(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG === true;
    }
}

/**
 * PSR-4 Style Autoloader for Modules
 */
spl_autoload_register(function ($class) {
    $prefix = 'Platinum\\Modules\\';
    $base_dir = __DIR__ . '/../modules/'; // Relative to bootstrap folder

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});