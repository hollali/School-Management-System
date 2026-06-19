<?php

namespace App\Listeners;

use App\Events\NotificationBroadcast;
use App\Events\SubmissionRetracted;
use App\Models\AppNotification;

class SendSubmissionRetractedNotification
{
    public function handle(SubmissionRetracted $event): void
    {
        $submission = $event->submission;
        $assignment = $submission->assignment;
        $teacher = $assignment?->teacher;

        if (!$teacher?->user) return;

        $notification = AppNotification::create([
            'type' => 'submission',
            'notifiable_type' => get_class($teacher->user),
            'notifiable_id' => $teacher->user->id,
            'data' => [
                'title' => 'Submission Withdrawn: ' . ($assignment->title ?? ''),
                'body' => ($submission->student?->user?->name ?? 'A student') . ' has withdrawn their submission for "' . ($assignment->title ?? '') . '"',
                'action_url' => route('assignments.show', $assignment),
                'type' => 'submission',
                'submission_id' => $submission->id,
                'assignment_id' => $assignment?->id,
                'student_name' => $submission->student?->user?->name ?? '',
                'retracted_at' => now()->toISOString(),
            ],
            'read_at' => null,
        ]);
        broadcast(new NotificationBroadcast($notification));
    }
}
