<?php
namespace Platinum\Modules\Training\Domain;

interface EnrollmentRepository
{
    public function exists(int $userId, int $trainingId): bool;
    public function save(Enrollment $enrollment): void;
}