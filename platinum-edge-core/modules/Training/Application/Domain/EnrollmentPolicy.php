<?php
namespace Platinum\Modules\Training\Domain;

final class EnrollmentPolicy
{
    /**
     * Rule: A student cannot enroll in the same training twice.
     * * @throws \DomainException
     */
    public function ensureUserNotAlreadyEnrolled(bool $alreadyExists): void
    {
        if ($alreadyExists) {
            throw new \DomainException("User is already enrolled in this training.");
        }
    }

    /**
     * Rule: Enrollments can only happen for active trainings.
     * (Placeholder for future logic like checking dates or capacity)
     */
    public function ensureTrainingIsEnrollable(array $trainingData): void
    {
        if (($trainingData['status'] ?? '') !== 'active') {
            throw new \DomainException("This training is not open for enrollment.");
        }
    }
}