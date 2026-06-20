<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            if (!Schema::hasColumn('attendance', 'subject_id')) {
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->nullOnDelete()->after('class_id');
            }
        });

        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            $table->date('attendance_date');
            $table->string('status')->default('present'); // present, absent, late, on_leave, excused
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('marked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['teacher_id', 'attendance_date']);
        });

        Schema::create('attendance_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('attendance_type'); // student, staff
            $table->unsignedBigInteger('attendance_id')->nullable();
            $table->unsignedBigInteger('record_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, corrected, deleted
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['attendance_type', 'attendance_id']);
            $table->index(['attendance_type', 'record_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_audit_logs');
        Schema::dropIfExists('staff_attendance');

        Schema::table('attendance', function (Blueprint $table) {
            if (Schema::hasColumn('attendance', 'subject_id')) {
                $table->dropConstrainedForeignId('subject_id');
            }
        });
    }
};
