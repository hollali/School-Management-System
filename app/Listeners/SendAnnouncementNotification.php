<?php

namespace App\Listeners;

use App\Events\AnnouncementPublished;
use App\Events\NotificationBroadcast;
use App\Models\Announcement;
use App\Models\AppNotification;
use App\Models\User;

class SendAnnouncementNotification
{
    public function handle(AnnouncementPublished $event): void
    {
        $announcement = $event->announcement;
        $users = User::query();

        if ($announcement->target_student_id) {
            $users->whereHas('student', function ($q) use ($announcement) {
                $q->where('id', $announcement->target_student_id);
            });
        } elseif ($announcement->target_class_id) {
            $users->whereHas('student.classes', function ($q) use ($announcement) {
                $q->where('classes.id', $announcement->target_class_id);
            });
        } elseif ($announcement->target_role) {
            $users->role(ucfirst($announcement->target_role));
        }

        if ($announcement->target_student_id || $announcement->target_class_id) {
            $users->orWhereHas('teacher.classes', function ($q) use ($announcement) {
                if ($announcement->target_class_id) {
                    $q->where('classes.id', $announcement->target_class_id);
                }
            });
        }

        $users->chunk(100, function ($chunk) use ($announcement) {
            foreach ($chunk as $user) {
                $notification = AppNotification::create([
                    'type' => 'announcement',
                    'notifiable_type' => get_class($user),
                    'notifiable_id' => $user->id,
                    'data' => [
                        'title' => $announcement->title,
                        'body' => substr($announcement->body, 0, 300),
                        'action_url' => route('announcements.show', $announcement),
                        'type' => 'announcement',
                        'announcement_id' => $announcement->id,
                        'published_by' => $announcement->publisher?->name ?? '',
                        'published_at' => $announcement->published_at?->toISOString(),
                    ],
                    'read_at' => null,
                ]);

                broadcast(new NotificationBroadcast($notification));
            }
        });
    }
}
