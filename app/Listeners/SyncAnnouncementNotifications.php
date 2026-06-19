<?php

namespace App\Listeners;

use App\Events\AnnouncementUpdated;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;
use App\Models\User;

class SyncAnnouncementNotifications
{
    public function handle(AnnouncementUpdated $event): void
    {
        $announcement = $event->announcement;

        $newUserIds = $this->getTargetUserIds($announcement);

        $existingUserIds = AppNotification::where('type', 'announcement')
            ->where('data->announcement_id', $announcement->id)
            ->pluck('notifiable_id')
            ->unique()
            ->values()
            ->toArray();

        $removeUserIds = array_diff($existingUserIds, $newUserIds);
        $addUserIds = array_diff($newUserIds, $existingUserIds);
        $keepUserIds = array_intersect($existingUserIds, $newUserIds);

        if (!empty($removeUserIds)) {
            AppNotification::where('type', 'announcement')
                ->where('data->announcement_id', $announcement->id)
                ->whereIn('notifiable_id', $removeUserIds)
                ->delete();
        }

        if (!empty($addUserIds)) {
            User::whereIn('id', $addUserIds)->chunk(100, function ($chunk) use ($announcement) {
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

        if (!empty($keepUserIds)) {
            AppNotification::where('type', 'announcement')
                ->where('data->announcement_id', $announcement->id)
                ->whereIn('notifiable_id', $keepUserIds)
                ->chunk(100, function ($notifications) use ($announcement) {
                    foreach ($notifications as $notification) {
                        $notifData = $notification->data;
                        $wasRead = !is_null($notification->read_at);
                        $notifData['title'] = '✎ Updated: ' . $announcement->title;
                        $notifData['body'] = substr($announcement->body, 0, 300);
                        $notifData['updated'] = true;
                        $notifData['updated_at'] = now()->toISOString();
                        $notification->update([
                            'data' => $notifData,
                            'read_at' => $wasRead ? $notification->read_at : null,
                        ]);
                    }
                });
        }
    }

    private function getTargetUserIds($announcement): array
    {
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

        return $users->pluck('id')->toArray();
    }
}
