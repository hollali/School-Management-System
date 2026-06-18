<?php

namespace App\Listeners;

use App\Events\AssignmentGraded;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendAssignmentGradedNotification
{
    public function handle(AssignmentGraded $event): void
    {
        $feedback = $event->feedback;
        $submission = $feedback->submission;
        $assignment = $submission?->assignment;
        $student = $submission?->student;

        if (!$student?->user) return;

        $notification = AppNotification::create([
            'type' => 'grade',
            'notifiable_type' => get_class($student->user),
            'notifiable_id' => $student->user->id,
            'data' => [
                'title' => 'Assignment Graded: ' . ($assignment->title ?? ''),
                'body' => 'Your submission for "' . ($assignment->title ?? '') . '" has been graded. '
                    . ($feedback->score !== null ? 'Score: ' . $feedback->score . '/100' : '')
                    . ($feedback->comments ? '. Feedback: ' . substr($feedback->comments, 0, 150) : ''),
                'action_url' => route('assignment-feedback.show', $feedback),
                'type' => 'grade',
                'score' => $feedback->score,
                'assignment_id' => $assignment?->id,
                'submission_id' => $submission?->id,
                'feedback_id' => $feedback->id,
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));
    }
}
