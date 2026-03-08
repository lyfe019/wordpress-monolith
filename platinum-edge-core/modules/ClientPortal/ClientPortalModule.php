<?php
namespace Platinum\Modules\ClientPortal;

use Platinum\Core\Modules\AbstractModule;
use Platinum\Core\Events\EventBus;
use Platinum\Modules\ClientPortal\ReadModel\TrainingEnrollmentProjector;

final class ClientPortalModule extends AbstractModule
{
    public function id(): string { return 'client-portal'; }
    public function name(): string { return 'Client Portal'; }

    public function register(): void {
        // Register services in container if needed
    }

    public function boot(): void
    {
        // Listen for events from the Training module
        EventBus::subscribe('training.enrolled', [new TrainingEnrollmentProjector(), 'handleEnrollment']);
    }
}