<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'grader_id',
        'marks_awarded',
        'marks_previous',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'marks_awarded' => 'decimal:2',
            'marks_previous' => 'decimal:2',
        ];
    }

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }

    public function grader()
    {
        return $this->belongsTo(Teacher::class, 'grader_id');
    }
}
