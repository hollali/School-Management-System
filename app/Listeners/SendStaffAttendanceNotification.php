<?php

namespace App\Listeners;

use App\Events\NotificationBroadcast;
use App\Events\StaffAttendanceMarked;
use App\Models\AppNotification;
use App\Models\User;

class SendStaffAttendanceNotification
{
    public function handle(StaffAttendanceMarked $event): void
    {
        $staffAttendance = $event->staffAttendance;
        $staffAttendance->loadMissing(['teacher.user']);

        $teacher = $staffAttendance->teacher;

        if (!$teacher?->user) return;

        $notification = AppNotification::create([
            'type' => 'attendance',
            'notifiable_type' => get_class($teacher->user),
            'notifiable_id' => $teacher->user->id,
            'data' => [
                'title' => 'Attendance Recorded',
                'body' => 'Your attendance has been recorded as '
                    . ucfirst(str_replace('_', ' ', $staffAttendance->status))
                    . ' for ' . ($staffAttendance->attendance_date?->format('M d, Y') ?? 'today'),
                'action_url' => route('staff-attendance.index'),
                'type' => 'attendance',
                'status' => $staffAttendance->status,
                'attendance_date' => $staffAttendance->attendance_date?->toISOString(),
            ],
            'read_at' => null,
        ]);

        broadcast(new NotificationBroadcast($notification));

        $adminUsers = User::role('Admin')->get();
        foreach ($adminUsers as $admin) {
            $adminNotification = AppNotification::create([
                'type' => 'attendance',
                'notifiable_type' => get_class($admin),
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'Staff Attendance',
                    'body' => ($teacher?->user?->name ?? 'A teacher') . ' has been marked as '
                        . ucfirst(str_replace('_', ' ', $staffAttendance->status))
                        . ' on ' . ($staffAttendance->attendance_date?->format('M d, Y') ?? 'today'),
                    'action_url' => route('staff-attendance.index'),
                    'type' => 'attendance',
                    'teacher_id' => $teacher?->id,
                    'status' => $staffAttendance->status,
                ],
                'read_at' => null,
            ]);

            broadcast(new NotificationBroadcast($adminNotification));
        }
    }
}
