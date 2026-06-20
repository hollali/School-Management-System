<?php

namespace App\Providers;

use App\Events\AnnouncementPublished;
use App\Events\AnnouncementUpdated;
use App\Events\AssignmentCreated;
use App\Events\AssignmentDeadlineUpdated;
use App\Events\AssignmentGraded;
use App\Events\AttendanceCorrected;
use App\Events\AttendanceThresholdReached;
use App\Events\FeeDiscountApplied;
use App\Events\FeeInvoiceGenerated;
use App\Events\FeeOverdueDetected;
use App\Events\FeePaymentMade;
use App\Events\MessageSent;
use App\Events\StaffAttendanceMarked;
use App\Events\StudentMarkedAbsent;
use App\Events\StudentMarkedLate;
use App\Events\SubmissionSubmitted;
use App\Events\SubmissionRetracted;
use App\Events\SubmissionRejected;
use App\Listeners\SendAbsentNotification;
use App\Listeners\SendAnnouncementNotification;
use App\Listeners\SyncAnnouncementNotifications;
use App\Listeners\SendAssignmentCreatedNotification;
use App\Listeners\SendAssignmentDeadlineUpdatedNotification;
use App\Listeners\SendAssignmentGradedNotification;
use App\Listeners\SendAttendanceCorrectionNotification;
use App\Listeners\SendDiscountAppliedNotification;
use App\Listeners\SendInvoiceGeneratedNotification;
use App\Listeners\SendLateNotification;
use App\Listeners\SendLowAttendanceNotification;
use App\Listeners\SendMessageNotification;
use App\Listeners\SendOverdueNotification;
use App\Listeners\SendPaymentConfirmationNotification;
use App\Listeners\SendStaffAttendanceNotification;
use App\Listeners\SendSubmissionReceivedNotification;
use App\Listeners\SendSubmissionRetractedNotification;
use App\Listeners\SendSubmissionRejectedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AssignmentCreated::class => [
            SendAssignmentCreatedNotification::class,
        ],
        AssignmentDeadlineUpdated::class => [
            SendAssignmentDeadlineUpdatedNotification::class,
        ],
        SubmissionSubmitted::class => [
            SendSubmissionReceivedNotification::class,
        ],
        AssignmentGraded::class => [
            SendAssignmentGradedNotification::class,
        ],
        MessageSent::class => [
            SendMessageNotification::class,
        ],
        AnnouncementPublished::class => [
            SendAnnouncementNotification::class,
        ],
        AnnouncementUpdated::class => [
            SyncAnnouncementNotifications::class,
        ],
        SubmissionRetracted::class => [
            SendSubmissionRetractedNotification::class,
        ],
        SubmissionRejected::class => [
            SendSubmissionRejectedNotification::class,
        ],
        StudentMarkedAbsent::class => [
            SendAbsentNotification::class,
        ],
        StudentMarkedLate::class => [
            SendLateNotification::class,
        ],
        AttendanceCorrected::class => [
            SendAttendanceCorrectionNotification::class,
        ],
        AttendanceThresholdReached::class => [
            SendLowAttendanceNotification::class,
        ],
        StaffAttendanceMarked::class => [
            SendStaffAttendanceNotification::class,
        ],
        FeeInvoiceGenerated::class => [
            SendInvoiceGeneratedNotification::class,
        ],
        FeePaymentMade::class => [
            SendPaymentConfirmationNotification::class,
        ],
        FeeOverdueDetected::class => [
            SendOverdueNotification::class,
        ],
        FeeDiscountApplied::class => [
            SendDiscountAppliedNotification::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
