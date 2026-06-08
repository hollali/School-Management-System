<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentFeedback extends Model
{
    use HasFactory;

    protected $table = 'assignment_feedback';

    protected $fillable = [
        'submission_id',
        'teacher_id',
        'comments',
        'score',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }
}
