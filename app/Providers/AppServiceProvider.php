<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentFeedback;
use App\Models\Attendance;
use App\Models\Conversation;
use App\Models\Exam;
use App\Models\Message;
use App\Models\Result;
use App\Models\Student;
use App\Models\Submission;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentFeedbackPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ConversationPolicy;
use App\Policies\ExamPolicy;
use App\Policies\MessagePolicy;
use App\Policies\ResultPolicy;
use App\Policies\StudentPolicy;
use App\Policies\SubmissionPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Announcement::class, AnnouncementPolicy::class);
        Gate::policy(Assignment::class, AssignmentPolicy::class);
        Gate::policy(Exam::class, ExamPolicy::class);
        Gate::policy(Attendance::class, AttendancePolicy::class);
        Gate::policy(Result::class, ResultPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(AssignmentFeedback::class, AssignmentFeedbackPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);

        Gate::define('manage-users', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-classes', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-subjects', fn ($user) => $user->hasRole('Admin'));
        Gate::define('view-reports', fn ($user) => $user->hasRole('Admin'));
    }
}
