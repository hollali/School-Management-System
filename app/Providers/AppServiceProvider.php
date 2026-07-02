<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\Assignment;
use App\Models\AssignmentFeedback;
use App\Models\Attendance;
use App\Models\Conversation;
use App\Models\Discount;
use App\Models\Exam;
use App\Models\Fee;
use App\Models\Holiday;
use App\Models\Message;
use App\Models\Payment;
use App\Models\QuestionBank;
use App\Models\Receipt;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Result;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\Submission;
use App\Policies\AnnouncementPolicy;
use App\Policies\AssignmentFeedbackPolicy;
use App\Policies\AssignmentPolicy;
use App\Policies\AttendancePolicy;
use App\Policies\ConversationPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\ExamPolicy;
use App\Policies\FeePolicy;
use App\Policies\HolidayPolicy;
use App\Policies\MessagePolicy;
use App\Policies\PaymentPolicy;
use App\Policies\QuestionBankPolicy;
use App\Policies\ReceiptPolicy;
use App\Policies\ResultPolicy;
use App\Policies\SchoolClassPolicy;
use App\Policies\SubjectPolicy;
use App\Policies\StaffAttendancePolicy;
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
        Gate::policy(StaffAttendance::class, StaffAttendancePolicy::class);
        Gate::policy(Result::class, ResultPolicy::class);
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Submission::class, SubmissionPolicy::class);
        Gate::policy(AssignmentFeedback::class, AssignmentFeedbackPolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Fee::class, FeePolicy::class);
        Gate::policy(Holiday::class, HolidayPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(Receipt::class, ReceiptPolicy::class);
        Gate::policy(Discount::class, DiscountPolicy::class);
        Gate::policy(QuestionBank::class, QuestionBankPolicy::class);
        Gate::policy(Subject::class, SubjectPolicy::class);
        Gate::policy(SchoolClass::class, SchoolClassPolicy::class);

        Gate::define('manage-users', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-classes', fn ($user) => $user->hasRole('Admin'));
        Gate::define('manage-subjects', fn ($user) => $user->hasRole('Admin'));
        Gate::define('view-reports', fn ($user) => $user->hasRole('Admin'));
    }
}
