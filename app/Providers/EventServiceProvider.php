<?php

namespace App\Providers;

use App\Events\AnnouncementPublished;
use App\Events\AssignmentCreated;
use App\Events\AssignmentDeadlineUpdated;
use App\Events\AssignmentGraded;
use App\Events\MessageSent;
use App\Events\SubmissionSubmitted;
use App\Listeners\SendAnnouncementNotification;
use App\Listeners\SendAssignmentCreatedNotification;
use App\Listeners\SendAssignmentDeadlineUpdatedNotification;
use App\Listeners\SendAssignmentGradedNotification;
use App\Listeners\SendMessageNotification;
use App\Listeners\SendSubmissionReceivedNotification;
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
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
