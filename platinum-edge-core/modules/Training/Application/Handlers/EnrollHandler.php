<?php
namespace Platinum\Modules\Training\Application\Handlers;

use Platinum\Modules\Training\Application\Commands\EnrollTraining;
use Platinum\Modules\Training\Application\EnrollmentResult;
use Platinum\Modules\Training\Domain\Enrollment;
use Platinum\Modules\Training\Domain\EnrollmentPolicy;
use Platinum\Modules\Training\Domain\EnrollmentRepository;
use Platinum\Modules\Training\Policy\EnrollmentAuthorization;
use Platinum\Core\Events\EventBus;

final class EnrollHandler
{
    public function __construct(
        private EnrollmentAuthorization $authorization,
        private EnrollmentPolicy $policy,
        private EnrollmentRepository $repository
    ) {}

    public function handle(EnrollTraining $command): EnrollmentResult
    {
        // 1. Authorization: Who is allowed to do this?
        $this->authorization->assertCanEnroll($command->actor);

        // 2. Domain Rule: Are they already in this training?
        $exists = $this->repository->exists($command->actor->id(), $command->trainingId);
        $this->policy->ensureUserNotAlreadyEnrolled($exists);

        // 3. Domain Action: Create the enrollment state
        $enrollment = Enrollment::enroll(
            $command->actor->id(), 
            $command->trainingId
        );

        // 4. Infrastructure: Save the change
        $this->repository->save($enrollment);

        // 5. Side Effects: Tell the rest of the monolith what happened
        EventBus::dispatch('training.enrolled', [
            'user_id'     => $enrollment->userId(),
            'training_id' => $enrollment->trainingId(),
            'timestamp'   => time()
        ]);

        return EnrollmentResult::success($enrollment);
    }
}