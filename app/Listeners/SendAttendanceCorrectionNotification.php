<?php

namespace App\Listeners;

use App\Events\AttendanceCorrected;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendAttendanceCorrectionNotification
{
    public function handle(AttendanceCorrected $event): void
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
                'title' => 'Attendance Corrected',
                'body' => 'Your attendance for '
                    . ($class?->name ?? 'class')
                    . ' on ' . ($record->attendance?->attendance_date?->format('M d, Y') ?? 'unknown date')
                    . ' has been changed from ' . ucfirst($event->oldStatus)
                    . ' to ' . ucfirst($event->newStatus),
                'action_url' => route('attendance.student.show'),
                'type' => 'attendance',
                'status' => $event->newStatus,
                'old_status' => $event->oldStatus,
                'attendance_date' => $record->attendance?->attendance_date?->toISOString(),
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));
    }
}
