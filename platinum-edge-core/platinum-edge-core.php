<?php
/**
 * Plugin Name: Platinum Edge Core
 * Description: Modular Monolith Infrastructure
 */

defined('ABSPATH') || exit;

use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Kernel;
use Platinum\Core\Api\HttpRequest;

// Bridge the WordPress REST API to the Platinum Kernel
add_action('rest_api_init', function () {
    register_rest_route('platinum/v1', '/(?P<path>.*)', [
        'methods'             => ['GET', 'POST', 'PUT', 'DELETE'],
        'callback'            => function (\WP_REST_Request $wpRequest) {
            $container = ServiceContainer::getInstance();
            
            // Lazy boot if for some reason the action didn't fire
            if (!$container->has('api_kernel')) {
                Kernel::boot();
            }

            $kernel  = $container->get('api_kernel');
            $request = HttpRequest::fromWp($wpRequest);

            return $kernel->handle($request);
        },
        'permission_callback' => '__return_true',
    ]);
});