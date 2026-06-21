<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'started_at',
        'submitted_at',
        'status',
        'total_score',
        'percentage_score',
        'result_status',
        'graded_by',
        'graded_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
            'total_score' => 'decimal:2',
            'percentage_score' => 'decimal:2',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function grader()
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class, 'attempt_id');
    }

    public function gradingLogs()
    {
        return $this->hasMany(GradingLog::class, 'attempt_id');
    }
}
