<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AssignmentFeedbackController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReactionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\FeeController;
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
    Route::resource('attendances', AttendanceController::class);
    Route::resource('attendance-records', AttendanceRecordController::class);

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
    Route::get('conversations-list/json', [ConversationController::class, 'conversationListJson'])->name('conversations.list.json');
    Route::get('users/search', [ConversationController::class, 'searchUsers'])->name('users.search');
    Route::get('users/available', [ConversationController::class, 'getAvailableUsersJson'])->name('users.available');
    Route::resource('conversations', ConversationController::class)->except(['show', 'edit', 'create', 'update']);
    Route::post('messages/{message}/forward', [MessageController::class, 'forward'])->name('messages.forward');
    Route::post('messages/{message}/reactions', [MessageReactionController::class, 'store'])->name('messages.reactions.store');
    Route::delete('messages/{message}/reactions/{reaction}', [MessageReactionController::class, 'destroy'])->name('messages.reactions.destroy');
    Route::resource('messages', MessageController::class)->only(['update', 'destroy']);

    // Fees
    Route::resource('fees', FeeController::class);
    Route::get('fees/export-csv', [FeeController::class, 'exportCsv'])->name('fees.export-csv');
    Route::resource('payments', PaymentController::class);
    Route::get('payments/export-csv', [PaymentController::class, 'exportCsv'])->name('payments.export-csv');
    Route::resource('receipts', ReceiptController::class);
    Route::get('receipts/export-csv', [ReceiptController::class, 'exportCsv'])->name('receipts.export-csv');

    // Announcements
    Route::resource('announcements', AnnouncementController::class);

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/{notification}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount'])->name('notifications.unreadCount');
});

require __DIR__.'/auth.php';
