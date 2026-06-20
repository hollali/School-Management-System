<?php

namespace App\Listeners;

use App\Events\AttendanceThresholdReached;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendLowAttendanceNotification
{
    public function handle(AttendanceThresholdReached $event): void
    {
        $student = $event->student;

        if (!$student?->user) return;

        $notification = AppNotification::create([
            'type' => 'attendance',
            'notifiable_type' => get_class($student->user),
            'notifiable_id' => $student->user->id,
            'data' => [
                'title' => 'Attendance Warning',
                'body' => 'Your attendance percentage has dropped to '
                    . round($event->percentage, 1) . '%, which is below the required threshold of '
                    . round($event->threshold, 1) . '%. Please contact your teacher.',
                'action_url' => route('attendance.student.show'),
                'type' => 'attendance',
                'percentage' => $event->percentage,
                'threshold' => $event->threshold,
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));

        $adminUsers = \App\Models\User::role('Admin')->get();
        foreach ($adminUsers as $admin) {
            $adminNotification = AppNotification::create([
                'type' => 'attendance',
                'notifiable_type' => get_class($admin),
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'Low Attendance Alert',
                    'body' => ($student?->user?->name ?? 'Student #' . $student->id)
                        . ' has attendance at ' . round($event->percentage, 1)
                        . '%, below the threshold of ' . round($event->threshold, 1) . '%.',
                    'action_url' => route('attendance.reports'),
                    'type' => 'attendance',
                    'student_id' => $student->id,
                    'percentage' => $event->percentage,
                    'threshold' => $event->threshold,
                ],
                'read_at' => null,
            ]);

            broadcast(new NotificationBroadcast($adminNotification));
        }
    }
}
