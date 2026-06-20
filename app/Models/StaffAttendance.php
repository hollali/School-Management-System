<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'teacher_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'remarks',
        'marked_by',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
            'check_in' => 'datetime',
            'check_out' => 'datetime',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
