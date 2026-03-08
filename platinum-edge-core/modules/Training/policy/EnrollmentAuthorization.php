<?php
namespace Platinum\Modules\Training\Policy;

use Platinum\Shared\Identity\Actor;

final class EnrollmentAuthorization
{
    /**
     * @throws \DomainException if the actor is not allowed to enroll
     */
    public function assertCanEnroll(Actor $actor): void
    {
        // 1. Must be authenticated
        if (!$actor->isAuthenticated()) {
            throw new \DomainException("Authentication required to enroll in training.", 401);
        }

        // 2. Specific capability check (independent of WP roles)
        if (!$actor->hasCapability('enroll_in_training')) {
            throw new \DomainException("You do not have permission to enroll in trainings.", 403);
        }
    }
}