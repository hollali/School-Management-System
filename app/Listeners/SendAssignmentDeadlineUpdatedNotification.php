<?php

namespace App\Listeners;

use App\Events\AssignmentDeadlineUpdated;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;
use App\Models\Student;

class SendAssignmentDeadlineUpdatedNotification
{
    public function handle(AssignmentDeadlineUpdated $event): void
    {
        $assignment = $event->assignment;
        $class = $assignment->schoolClass;

        if (!$class) return;

        $students = Student::whereHas('classes', function ($q) use ($class) {
            $q->where('classes.id', $class->id);
        })->with('user')->get();

        foreach ($students as $student) {
            if (!$student->user) continue;

            $notification = AppNotification::create([
                'type' => 'assignment',
                'notifiable_type' => get_class($student->user),
                'notifiable_id' => $student->user->id,
                'data' => [
                    'title' => 'Deadline Updated: ' . $assignment->title,
                    'body' => 'The due date for "' . $assignment->title . '" has been updated to '
                        . ($assignment->due_date?->format('M d, Y') ?? 'no date set'),
                    'action_url' => route('assignments.show', $assignment),
                    'type' => 'assignment',
                    'assignment_id' => $assignment->id,
                    'due_date' => $assignment->due_date?->toISOString(),
                ],
                'read_at' => null,
            ]);

            broadcast(new NotificationBroadcast($notification));
        }
    }
}
