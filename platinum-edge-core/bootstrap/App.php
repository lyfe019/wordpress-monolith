<?php
namespace Platinum\Core;

use Platinum\Core\Container\ServiceContainer;
use Platinum\Core\Kernel;

final class App
{
    private static bool $booted = false;

    /**
     * The App boot sequence is the only place where Domain Modules 
     * are explicitly attached to the system.
     */
    public static function boot(): void
    {
        if (self::$booted) return;

        // 1. Force Kernel boot to ensure Container & ModuleLoader are ready
        Kernel::boot();

        $container = ServiceContainer::getInstance();
        $loader    = $container->get('module_loader');

        // 2. Explicit Manifest: This is your "Source of Truth" for active features.
        // The ModuleLoader handles the two-phase (register -> boot) sequence.
        $loader->add(new \Platinum\Modules\Training\TrainingModule());
        $loader->add(new \Platinum\Modules\ClientPortal\ClientPortalModule());

        // 3. Execute the governed boot sequence
        $loader->boot();

        self::$booted = true;
    }
}