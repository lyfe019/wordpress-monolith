<?php
namespace Platinum\Modules\ClientPortal\ReadModel;

final class TrainingEnrollmentProjector
{
    /**
     * Reacts to 'training.enrolled' event.
     * Updates the portal_enrollments read-model table.
     */
    public function handleEnrollment(array $payload): void
    {
        global $wpdb;
        $table = $wpdb->prefix . 'platinum_portal_view';

        // Projecting a simplified view for the UI
        $wpdb->replace($table, [
            'user_id'     => $payload['user_id'],
            'training_id' => $payload['training_id'],
            'enrolled_at' => date('Y-m-d H:i:s', $payload['timestamp']),
            'status'      => 'In Progress' // UI-friendly label
        ]);
    }
}