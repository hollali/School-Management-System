<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionBank extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'question_bank';

    protected $fillable = [
        'subject_id',
        'teacher_id',
        'question_text',
        'question_type',
        'default_marks',
        'difficulty',
        'topic',
        'explanation',
        'image',
        'tags',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'default_marks' => 'decimal:2',
            'tags' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function options()
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
    }

    public function correctOptions()
    {
        return $this->options()->where('is_correct', true);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_questions', 'question_id', 'exam_id')
            ->withPivot(['marks', 'order'])
            ->withTimestamps();
    }
}
