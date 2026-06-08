<?php

use App\Http\Controllers\StudentApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    // Students API
    Route::apiResource('students', StudentApiController::class);
    Route::get('students/{student}/classes', [StudentApiController::class, 'getClasses']);
    Route::get('students/{student}/attendance', [StudentApiController::class, 'getAttendance']);
    Route::get('students/{student}/grades', [StudentApiController::class, 'getGrades']);
    Route::post('students/{student}/assign-class', [StudentApiController::class, 'assignClass']);
    Route::post('students/bulk-import', [StudentApiController::class, 'bulkImport']);
});
