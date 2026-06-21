<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamQuestion extends Pivot
{
    protected $table = 'exam_questions';

    protected $fillable = [
        'exam_id',
        'question_id',
        'marks',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
        ];
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(QuestionBank::class, 'question_id');
    }
}
