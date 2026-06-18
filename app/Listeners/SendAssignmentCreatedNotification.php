<?php

namespace App\Listeners;

use App\Events\AssignmentCreated;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;
use App\Models\Student;
use App\Models\User;

class SendAssignmentCreatedNotification
{
    public function handle(AssignmentCreated $event): void
    {
        $assignment = $event->assignment;
        $teacher = $assignment->teacher;
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
                    'title' => 'New Assignment: ' . $assignment->title,
                    'body' => $assignment->description
                        ? substr($assignment->description, 0, 200)
                        : 'A new assignment has been posted in ' . ($class->name ?? 'your class'),
                    'action_url' => route('assignments.show', $assignment),
                    'type' => 'assignment',
                    'assignment_id' => $assignment->id,
                    'class_name' => $class->name ?? '',
                    'due_date' => $assignment->due_date?->toISOString(),
                    'teacher_name' => $teacher?->user?->name ?? '',
                ],
                'read_at' => null,
            ]);

            broadcast(new NotificationBroadcast($notification));
        }
    }
}
