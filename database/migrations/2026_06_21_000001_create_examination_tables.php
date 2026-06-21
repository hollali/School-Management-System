<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Academic terms
        Schema::create('academic_terms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });

        // 2. Extend exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('subject_id')->nullable()->after('teacher_id')->constrained()->nullOnDelete();
            $table->foreignId('class_id')->nullable()->after('subject_id')->constrained('classes')->nullOnDelete();
            $table->foreignId('academic_term_id')->nullable()->after('class_id')->constrained('academic_terms')->nullOnDelete();
            $table->text('description')->nullable()->after('academic_year');
            $table->text('instructions')->nullable()->after('description');
            $table->integer('duration_minutes')->nullable()->after('instructions');
            $table->decimal('total_marks', 8, 2)->nullable()->after('duration_minutes');
            $table->decimal('pass_mark', 8, 2)->nullable()->after('total_marks');
            $table->integer('max_attempts')->default(1)->after('pass_mark');
            $table->enum('status', ['draft', 'published', 'in_progress', 'completed', 'archived'])->default('draft')->after('max_attempts');
            $table->enum('exam_mode', ['online', 'offline'])->default('offline')->after('status');
            $table->boolean('is_published')->default(false)->after('exam_mode');
            $table->boolean('results_published')->default(false)->after('is_published');
            $table->boolean('show_results_immediately')->default(false)->after('results_published');
            $table->boolean('shuffle_questions')->default(false)->after('show_results_immediately');
            $table->boolean('shuffle_options')->default(false)->after('shuffle_questions');
            $table->boolean('negative_marking')->default(false)->after('shuffle_options');
            $table->decimal('negative_mark_value', 8, 2)->default(0)->after('negative_marking');
            $table->boolean('fullscreen_required')->default(false)->after('negative_mark_value');
            $table->boolean('tab_switch_detection')->default(false)->after('fullscreen_required');
            $table->boolean('copy_paste_disabled')->default(false)->after('tab_switch_detection');
            $table->dateTime('start_date')->nullable()->after('copy_paste_disabled');
            $table->dateTime('end_date')->nullable()->after('start_date');
        });

        // 3. Question bank
        Schema::create('question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->string('question_text');
            $table->enum('question_type', ['mcq', 'true_false', 'fill_blank', 'short_answer', 'essay', 'matching', 'multi_select', 'numeric']);
            $table->decimal('default_marks', 8, 2)->default(1);
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->string('topic')->nullable();
            $table->text('explanation')->nullable();
            $table->string('image')->nullable();
            $table->json('tags')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Question options
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained('question_bank')->cascadeOnDelete();
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // 5. Exam-question pivot
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_bank')->cascadeOnDelete();
            $table->decimal('marks', 8, 2)->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->unique(['exam_id', 'question_id']);
        });

        // 6. Exam schedules
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();
            $table->date('exam_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['exam_id', 'class_id']);
        });

        // 7. Exam attempts
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'graded'])->default('not_started');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('percentage_score', 8, 2)->nullable();
            $table->enum('result_status', ['pass', 'fail', 'pending'])->default('pending');
            $table->foreignId('graded_by')->nullable()->constrained('teachers')->nullOnDelete();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'exam_id']);
        });

        // 8. Student answers
        Schema::create('student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_bank')->cascadeOnDelete();
            $table->foreignId('selected_option_id')->nullable()->constrained('question_options')->nullOnDelete();
            $table->text('answer_text')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('marks_obtained', 8, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
            $table->unique(['attempt_id', 'question_id']);
        });

        // 9. Grading logs
        Schema::create('grading_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('question_bank')->cascadeOnDelete();
            $table->foreignId('grader_id')->nullable()->constrained('teachers')->nullOnDelete();
            $table->decimal('marks_awarded', 8, 2);
            $table->decimal('marks_previous', 8, 2)->nullable();
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // 10. Add is_published to results
        Schema::table('results', function (Blueprint $table) {
            $table->boolean('is_published')->default(false)->after('remarks');
            $table->foreignId('graded_by')->nullable()->after('teacher_id')->constrained('teachers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grading_logs');
        Schema::dropIfExists('student_answers');
        Schema::dropIfExists('exam_attempts');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('question_bank');
        Schema::dropIfExists('academic_terms');

        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['academic_term_id']);
            $table->dropColumn([
                'subject_id', 'class_id', 'academic_term_id', 'description', 'instructions',
                'duration_minutes', 'total_marks', 'pass_mark', 'max_attempts', 'status',
                'exam_mode', 'is_published', 'results_published', 'show_results_immediately',
                'shuffle_questions', 'shuffle_options', 'negative_marking', 'negative_mark_value',
                'fullscreen_required', 'tab_switch_detection', 'copy_paste_disabled',
                'start_date', 'end_date',
            ]);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['is_published', 'graded_by']);
        });
    }
};
