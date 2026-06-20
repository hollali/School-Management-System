<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'student_id',
        'status',
        'remarks',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForDateRange($query, $start, $end)
    {
        return $query->whereHas('attendance', function ($q) use ($start, $end) {
            $q->whereBetween('attendance_date', [$start, $end]);
        });
    }
}
