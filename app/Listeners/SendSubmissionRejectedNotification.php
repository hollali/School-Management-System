<?php

namespace App\Listeners;

use App\Events\NotificationBroadcast;
use App\Events\SubmissionRejected;
use App\Models\AppNotification;

class SendSubmissionRejectedNotification
{
    public function handle(SubmissionRejected $event): void
    {
        $submission = $event->submission;
        $assignment = $submission->assignment;
        $student = $submission->student;

        if (!$student?->user) return;

        $notification = AppNotification::create([
            'type' => 'submission',
            'notifiable_type' => get_class($student->user),
            'notifiable_id' => $student->user->id,
            'data' => [
                'title' => 'Submission Rejected: ' . ($assignment->title ?? ''),
                'body' => 'Your submission for "' . ($assignment->title ?? '') . '" was rejected. Reason: ' . $event->reason,
                'action_url' => route('assignments.show', $assignment),
                'type' => 'submission',
                'submission_id' => $submission->id,
                'assignment_id' => $assignment?->id,
                'rejection_reason' => $event->reason,
                'rejected_at' => now()->toISOString(),
            ],
            'read_at' => null,
        ]);
        broadcast(new NotificationBroadcast($notification));
    }
}
