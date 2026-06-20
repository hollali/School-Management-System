<?php

namespace App\Listeners;

use App\Events\FeePaymentMade;
use App\Events\NotificationBroadcast;
use App\Models\AppNotification;

class SendPaymentConfirmationNotification
{
    public function handle(FeePaymentMade $event): void
    {
        $payment = $event->payment;
        $payment->loadMissing(['fee.student.user', 'fee.student.parent.user', 'parentProfile.user']);

        $fee = $payment->fee;
        $student = $fee?->student;
        if (!$student) return;

        $studentUser = $student->user;
        $parentUser = $student->parent?->user;

        $title = 'Payment Received';
        $body = 'A payment of $' . number_format($payment->amount, 2)
            . ' has been received for invoice ' . ($fee->invoice_number ?? '')
            . '. Remaining balance: $' . number_format($fee->balance ?? 0, 2);

        $notificationData = [
            'title' => $title,
            'body' => $body,
            'action_url' => route('receipts.show', $payment->receipt),
            'type' => 'fee',
            'payment_amount' => $payment->amount,
            'invoice_number' => $fee->invoice_number,
            'balance' => $fee->balance ?? 0,
            'receipt_number' => $payment->receipt?->receipt_number,
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
                    'body' => 'Payment for ' . ($student->user?->name ?? 'your child') . ': ' . $body,
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
                    'title' => 'Payment Received',
                    'body' => ($payment->parentProfile?->user?->name ?? 'A user')
                        . ' paid $' . number_format($payment->amount, 2)
                        . ' for ' . ($student->user?->name ?? 'student')
                        . ' (' . ($fee->invoice_number ?? '') . ')',
                    'action_url' => route('payments.show', $payment),
                    'type' => 'fee',
                    'payment_amount' => $payment->amount,
                ],
                'read_at' => null,
            ]);
            broadcast(new NotificationBroadcast($n));
        }
    }
}
