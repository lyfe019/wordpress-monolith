<?php
namespace Platinum\Modules\Training\Infrastructure;

use Platinum\Modules\Training\Domain\Enrollment;
use Platinum\Modules\Training\Domain\EnrollmentRepository as RepositoryInterface;

final class EnrollmentRepository implements RepositoryInterface
{
    public function exists(int $userId, int $trainingId): bool
    {
        global $wpdb;
        $table = $wpdb->prefix . 'platinum_enrollments';
        
        $row = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM $table WHERE user_id = %d AND training_id = %d LIMIT 1",
            $userId,
            $trainingId
        ));

        return !empty($row);
    }

    public function save(Enrollment $enrollment): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'platinum_enrollments';

        $wpdb->insert($table, [
            'user_id'     => $enrollment->userId(),
            'training_id' => $enrollment->trainingId(),
            'status'      => $enrollment->status(),
            'enrolled_at' => $enrollment->enrolledAt()->format('Y-m-d H:i:s'),
        ]);
    }
}