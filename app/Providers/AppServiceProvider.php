<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ExamPolicy;
use App\Policies\ResultPolicy;
use App\Policies\StudentPolicy;
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

        Gate::define('manage-users', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-classes', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-subjects', fn ($user) => $user->hasRole('Admin'));
        Gate::define('view-reports', fn ($user) => $user->hasRole('Admin'));
    }
}
