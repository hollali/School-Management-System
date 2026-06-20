<?php

namespace App\Listeners;

use App\Events\FeeInvoiceGenerated;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendInvoiceGeneratedNotification
{
    public function handle(FeeInvoiceGenerated $event): void
    {
        $fee = $event->fee;
        $fee->loadMissing(['student.user', 'student.parent.user']);

        $student = $fee->student;
        if (!$student) return;

        $studentUser = $student->user;
        $parentUser = $student->parent?->user;

        $title = 'New Invoice: ' . ($fee->invoice_number ?? '');
        $body = 'An invoice of $' . number_format($fee->balance, 2)
            . ' has been generated. Due date: '
            . ($fee->due_date?->format('M d, Y') ?? 'Not set');

        $notificationData = [
            'title' => $title,
            'body' => $body,
            'action_url' => route('fees.show', $fee),
            'type' => 'fee',
            'invoice_number' => $fee->invoice_number,
            'amount' => $fee->balance,
            'due_date' => $fee->due_date?->toISOString(),
        ];

        if ($studentUser) {
            $notification = AppNotification::create([
                'type' => 'fee',
                'notifiable_type' => get_class($studentUser),
                'notifiable_id' => $studentUser->id,
                'data' => $notificationData,
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($notification));
        }

        if ($parentUser) {
            $notification = AppNotification::create([
                'type' => 'fee',
                'notifiable_type' => get_class($parentUser),
                'notifiable_id' => $parentUser->id,
                'data' => array_merge($notificationData, [
                    'body' => 'Invoice for ' . ($student->user?->name ?? 'your child') . ': ' . $body,
                ]),
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($notification));
        }
    }
}
