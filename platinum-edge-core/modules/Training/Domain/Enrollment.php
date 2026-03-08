<?php
namespace Platinum\Modules\Training\Domain;

/**
 * Enrollment Entity
 * Represents the "Truth" of a student-training relationship.
 */
final class Enrollment
{
    private function __construct(
        private int $userId,
        private int $trainingId,
        private \DateTimeImmutable $enrolledAt,
        private string $status = 'active'
    ) {}

    /**
     * Named Constructor (Static Factory)
     * We don't just "new" up objects; we express intent.
     */
    public static function enroll(int $userId, int $trainingId): self
    {
        return new self(
            $userId,
            $trainingId,
            new \DateTimeImmutable()
        );
    }

    public function userId(): int { return $this->userId; }
    public function trainingId(): int { return $this->trainingId; }
    public function enrolledAt(): \DateTimeImmutable { return $this->enrolledAt; }
    public function status(): string { return $this->status; }
}