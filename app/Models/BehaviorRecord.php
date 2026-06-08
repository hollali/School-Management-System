<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviorRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'recorded_by',
        'type',
        'points',
        'description',
        'recorded_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
