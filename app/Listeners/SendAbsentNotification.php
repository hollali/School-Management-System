<?php

namespace App\Listeners;

use App\Events\NotificationBroadcast;
use App\Events\StudentMarkedAbsent;
use App\Models\AppNotification;
use Illuminate\Support\Facades\Log;

class SendAbsentNotification
{
    public function handle(StudentMarkedAbsent $event): void
    {
        $record = $event->record;
        $record->loadMissing(['attendance.schoolClass', 'student.user']);

        $student = $record->student;
        $class = $record->attendance?->schoolClass;

        if (!$student?->user) return;

        $notification = AppNotification::create([
            'type' => 'attendance',
            'notifiable_type' => get_class($student->user),
            'notifiable_id' => $student->user->id,
            'data' => [
                'title' => 'Marked Absent',
                'body' => 'You have been marked absent for '
                    . ($class?->name ?? 'class')
                    . ' on ' . ($record->attendance?->attendance_date?->format('M d, Y') ?? 'unknown date'),
                'action_url' => route('attendance.student.show'),
                'type' => 'attendance',
                'status' => 'absent',
                'attendance_date' => $record->attendance?->attendance_date?->toISOString(),
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));
    }
}
