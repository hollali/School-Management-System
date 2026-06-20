<?php

namespace App\Listeners;

use App\Events\FeeOverdueDetected;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendOverdueNotification
{
    public function handle(FeeOverdueDetected $event): void
    {
        $fee = $event->fee;
        $fee->loadMissing(['student.user', 'student.parent.user']);

        $student = $fee->student;
        if (!$student) return;

        $studentUser = $student->user;
        $parentUser = $student->parent?->user;

        $title = 'Payment Overdue';
        $body = 'Invoice ' . ($fee->invoice_number ?? '')
            . ' of $' . number_format($fee->balance, 2)
            . ' is overdue. Please make payment as soon as possible.';

        $notificationData = [
            'title' => $title,
            'body' => $body,
            'action_url' => route('fees.show', $fee),
            'type' => 'fee',
            'invoice_number' => $fee->invoice_number,
            'balance' => $fee->balance,
            'due_date' => $fee->due_date?->toISOString(),
        ];

        if ($studentUser) {
            $n = AppNotification::create([
                'type' => 'fee',
                'notifiable_type' => get_class($studentUser),
                'notifiable_id' => $studentUser->id,
                'data' => $notificationData,
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($n));
        }

        if ($parentUser) {
            $n = AppNotification::create([
                'type' => 'fee',
                'notifiable_type' => get_class($parentUser),
                'notifiable_id' => $parentUser->id,
                'data' => array_merge($notificationData, [
                    'body' => 'Payment overdue for ' . ($student->user?->name ?? 'your child') . ': ' . $body,
                ]),
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($n));
        }

        $adminUsers = \App\Models\User::role('Admin')->get();
        foreach ($adminUsers as $admin) {
            $n = AppNotification::create([
                'type' => 'fee',
                'notifiable_type' => get_class($admin),
                'notifiable_id' => $admin->id,
                'data' => [
                    'title' => 'Overdue Invoice',
                    'body' => ($student->user?->name ?? 'A student') . ' has an overdue invoice '
                        . ($fee->invoice_number ?? '') . ' of $' . number_format($fee->balance, 2),
                    'action_url' => route('fees.show', $fee),
                    'type' => 'fee',
                ],
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($n));
        }
    }
}
