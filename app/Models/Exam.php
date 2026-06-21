<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'exam_date',
        'term',
        'academic_year',
        'teacher_id',
        'subject_id',
        'class_id',
        'academic_term_id',
        'description',
        'instructions',
        'duration_minutes',
        'total_marks',
        'pass_mark',
        'max_attempts',
        'status',
        'exam_mode',
        'is_published',
        'results_published',
        'show_results_immediately',
        'shuffle_questions',
        'shuffle_options',
        'negative_marking',
        'negative_mark_value',
        'fullscreen_required',
        'tab_switch_detection',
        'copy_paste_disabled',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'exam_date' => 'date',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'is_published' => 'boolean',
            'results_published' => 'boolean',
            'show_results_immediately' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'negative_marking' => 'boolean',
            'fullscreen_required' => 'boolean',
            'tab_switch_detection' => 'boolean',
            'copy_paste_disabled' => 'boolean',
            'total_marks' => 'decimal:2',
            'pass_mark' => 'decimal:2',
            'negative_mark_value' => 'decimal:2',
        ];
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicTerm()
    {
        return $this->belongsTo(AcademicTerm::class);
    }

    public function questions()
    {
        return $this->belongsToMany(QuestionBank::class, 'exam_questions', 'exam_id', 'question_id')
            ->withPivot(['marks', 'order'])
            ->withTimestamps()
            ->orderByPivot('order');
    }

    public function schedules()
    {
        return $this->hasMany(ExamSchedule::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function isOnline()
    {
        return $this->exam_mode === 'online';
    }

    public function isActive()
    {
        $now = now();
        return $this->is_published
            && $this->status !== 'archived'
            && (!$this->start_date || $now >= $this->start_date)
            && (!$this->end_date || $now <= $this->end_date);
    }
}
