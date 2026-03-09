<?php
namespace Platinum\Tests\Bdd\Support;

use Platinum\Core\Kernel;
use Platinum\Core\Container\ServiceContainer;
use Platinum\Modules\Training\Domain\EnrollmentRepository;

final class TestKernel
{
    public static function boot(): void
    {
        // 1. Boot the real infrastructure first
        Kernel::boot();

        // 2. SWAP: Replace the MySQL Repository with an In-Memory version
        $container = ServiceContainer::getInstance();
        $container->singleton(EnrollmentRepository::class, function() {
            return new InMemoryEnrollmentRepository();
        });

        // 3. Clear Event Bus to ensure no leftover listeners from other tests
        \Platinum\Core\Events\EventBus::reset();
    }
}