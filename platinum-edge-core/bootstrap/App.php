<?php
namespace Platinum\Core;

use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Kernel;

final class App
{
    private static bool $booted = false;

    public static function boot(): void
    {
        if (self::$booted) return;

        // Force Kernel boot if someone called App::boot() directly
        Kernel::boot();

        $container = ServiceContainer::getInstance();
        $loader = $container->get('module_loader');

        // Explicit Manifest
        $loader->add(new \Platinum\Modules\Training\TrainingModule());
        $loader->add(new \Platinum\Modules\ClientPortal\ClientPortalModule());

        $loader->boot();
        self::$booted = true;
    }
}