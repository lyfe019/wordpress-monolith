<?php
/**
 * Plugin Name: Platinum Edge Core (MU)
 * Description: Core infrastructure for the Platinum Edge Modular Monolith.
 */

 // Crucial file
defined('ABSPATH') || exit;

// 1. THE ONLY REQUIRE YOU NEED: Load the Composer Autoloader
// This automatically handles everything that used to be in sections 1, 2, 3, 4, and 5.
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// 2. THE BOOT LOGIC (Now handled via the classes found by the autoloader)
use Platinum\Core\Kernel;
use Platinum\Core\App;
use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Api\HttpRequest;

// 3. BOOT ORDER (Ensure we don't boot twice if 00-platinum-loader already did it)
add_action('plugins_loaded', function() {
    Kernel::boot();
    App::boot();
}, 10);

// 4. WORDPRESS → PLATINUM API BRIDGE
add_action('rest_api_init', function () {
    register_rest_route('platinum/v1', '/(?P<path>.*)', [
        'methods'  => ['GET', 'POST', 'PUT', 'DELETE'],
        'callback' => function (\WP_REST_Request $wpRequest) {
            
            $container = ServiceContainer::getInstance();

            // Safety check
            if (!$container->has('api_kernel')) {
                Kernel::boot();
            }

            $kernel = $container->get('api_kernel');

            // Convert WP request → Platinum request
            $request = HttpRequest::fromWp($wpRequest);

            // Let Platinum handle response
            $result = $kernel->handle($request);
            
            // Note: If your handle() method returns an array or object, 
            // WP REST expects a return value here.
            return $result;
        },
        'permission_callback' => '__return_true',
    ]);
});