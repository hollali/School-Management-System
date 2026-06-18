<?php

namespace App\Listeners;

use App\Events\NotificationBroadcast;
use App\Events\SubmissionSubmitted;
use App\Models\AppNotification;

class SendSubmissionReceivedNotification
{
    public function handle(SubmissionSubmitted $event): void
    {
        $submission = $event->submission;
        $assignment = $submission->assignment;
        $teacher = $assignment?->teacher;
        $student = $submission->student;

        if (!$teacher?->user) return;

        $notification = AppNotification::create([
            'type' => 'submission',
            'notifiable_type' => get_class($teacher->user),
            'notifiable_id' => $teacher->user->id,
            'data' => [
                'title' => 'Submission Received: ' . ($assignment->title ?? ''),
                'body' => ($student?->user?->name ?? 'A student') . ' has submitted "' . ($assignment->title ?? '') . '"',
                'action_url' => route('submissions.show', $submission),
                'type' => 'submission',
                'submission_id' => $submission->id,
                'assignment_id' => $assignment?->id,
                'student_name' => $student?->user?->name ?? '',
                'submitted_at' => $submission->submitted_at?->toISOString(),
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));
    }
}
