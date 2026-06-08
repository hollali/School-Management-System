<?php

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
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\FeeController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // School Management resources
    Route::resource('students', StudentController::class);
    Route::resource('school-classes', SchoolClassController::class);

    // Attendance
    Route::resource('attendances', AttendanceController::class);
    Route::resource('attendance-records', AttendanceRecordController::class);

    // Homework
    Route::resource('assignments', AssignmentController::class);
    Route::resource('submissions', SubmissionController::class);
    Route::resource('assignment-feedback', AssignmentFeedbackController::class);

    // Messaging
    Route::resource('conversations', ConversationController::class);
    Route::resource('messages', MessageController::class);

    // Academics
    Route::resource('subjects', SubjectController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('results', ResultController::class);

    // Fees
    Route::resource('fees', FeeController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('receipts', ReceiptController::class);
});

require __DIR__.'/auth.php';
