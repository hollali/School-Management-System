<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceDashboardController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\StaffAttendanceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AssignmentFeedbackController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\FeeCategoryController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FinanceDashboardController;
use App\Http\Controllers\FinanceReportController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin - User Management
    Route::middleware('role:Admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::get('class-assignments', [SchoolClassController::class, 'assignmentPage'])->name('class-assignments');
        Route::post('class-assignments', [SchoolClassController::class, 'bulkAssign'])->name('class-assignments.store');
    });

    // Student Management
    Route::resource('students', StudentController::class);
    Route::get('students/export-csv', [StudentController::class, 'exportCsv'])->name('students.export-csv');

    // Classes
    Route::resource('classes', SchoolClassController::class);
    Route::post('classes/{class}/students', [SchoolClassController::class, 'assignStudent'])->name('classes.students.assign');
    Route::delete('classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])->name('classes.students.remove');

    // Attendance
    Route::get('attendance/mark', [AttendanceController::class, 'mark'])->name('attendance.mark');
    Route::post('attendance/mark', [AttendanceController::class, 'storeMark'])->name('attendance.mark.store');
    Route::get('attendance/student', [AttendanceController::class, 'studentShow'])->name('attendance.student.show');
    Route::put('attendance/records/{record}', [AttendanceController::class, 'updateRecord'])->name('attendance.records.update');
    Route::get('attendance/dashboard', [AttendanceDashboardController::class, 'index'])->name('attendance.dashboard');
    Route::get('attendance/reports', [AttendanceReportController::class, 'index'])->name('attendance.reports');
    Route::resource('attendance', AttendanceController::class)->except(['create', 'edit']);

    // Staff Attendance
    Route::post('staff-attendance/check-in', [StaffAttendanceController::class, 'checkIn'])->name('staff-attendance.check-in');
    Route::post('staff-attendance/check-out', [StaffAttendanceController::class, 'checkOut'])->name('staff-attendance.check-out');
    Route::resource('staff-attendance', StaffAttendanceController::class);

    // Holidays
    Route::resource('holidays', HolidayController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

    // Academics
    Route::resource('subjects', SubjectController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('results', ResultController::class);

    // Homework
    Route::resource('assignments', AssignmentController::class);
    Route::resource('submissions', SubmissionController::class);
    Route::post('submissions/{submission}/retract', [SubmissionController::class, 'retract'])->name('submissions.retract');
    Route::post('submissions/{submission}/reject', [SubmissionController::class, 'reject'])->name('submissions.reject');
    Route::resource('assignment-feedback', AssignmentFeedbackController::class);

    // Messaging
    Route::get('conversations/{conversation}/messages', [MessageController::class, 'index'])->name('conversations.messages');
    Route::post('conversations/{conversation}/messages', [MessageController::class, 'store'])->name('conversations.messages.store');
    Route::post('conversations/{conversation}/read', [ConversationController::class, 'markAsRead'])->name('conversations.read');
    Route::post('conversations/{conversation}/archive', [ConversationController::class, 'toggleArchive'])->name('conversations.archive');
    Route::post('conversations/{conversation}/pin', [ConversationController::class, 'togglePin'])->name('conversations.pin');
    Route::get('conversations/unread/total', [ConversationController::class, 'totalUnreadCount'])->name('conversations.unread.total');
    Route::get('conversations-list/json', [ConversationController::class, 'conversationListJson'])->name('conversations.list.json');
    Route::get('users/search', [ConversationController::class, 'searchUsers'])->name('users.search');
    Route::get('users/available', [ConversationController::class, 'getAvailableUsersJson'])->name('users.available');
    Route::resource('conversations', ConversationController::class)->except(['show', 'edit', 'create', 'update']);
    Route::post('messages/{message}/forward', [MessageController::class, 'forward'])->name('messages.forward');
    Route::post('messages/{message}/reactions', [MessageReactionController::class, 'store'])->name('messages.reactions.store');
    Route::delete('messages/{message}/reactions/{reaction}', [MessageReactionController::class, 'destroy'])->name('messages.reactions.destroy');
    Route::resource('messages', MessageController::class)->only(['update', 'destroy']);

    // Fees
    Route::get('fees/generate', [FeeController::class, 'generateInvoices'])->name('fees.generate');
    Route::post('fees/generate', [FeeController::class, 'generateInvoices'])->name('fees.generate.store');
    Route::resource('fees', FeeController::class);
    Route::get('fees/export-csv', [FeeController::class, 'exportCsv'])->name('fees.export-csv');
    Route::resource('fee-structures', FeeStructureController::class);
    Route::resource('fee-categories', FeeCategoryController::class);
    Route::resource('payments', PaymentController::class);
    Route::get('payments/export-csv', [PaymentController::class, 'exportCsv'])->name('payments.export-csv');
    Route::get('payments/parent/history', [PaymentController::class, 'parentHistory'])->name('payments.parent.history');
    Route::post('payments/parent/pay', [PaymentController::class, 'parentPay'])->name('payments.parent.pay');
    Route::resource('receipts', ReceiptController::class);
    Route::get('receipts/export-csv', [ReceiptController::class, 'exportCsv'])->name('receipts.export-csv');
    Route::resource('discounts', DiscountController::class);
    Route::get('finance/dashboard', [FinanceDashboardController::class, 'index'])->name('finance.dashboard');
    Route::get('finance/reports', [FinanceReportController::class, 'index'])->name('finance.reports');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
});

require __DIR__.'/auth.php';
