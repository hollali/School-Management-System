<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'class_id',
        'teacher_id',
        'subject_id',
        'attendance_date',
        'created_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'attendance_date' => 'date',
        ];
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class, 'attendance_id');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->where('attendance_date', $date);
    }

    public function scopeForDateRange($query, $start, $end)
    {
        return $query->whereBetween('attendance_date', [$start, $end]);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->whereHas('records', function ($q) use ($studentId) {
            $q->where('student_id', $studentId);
        });
    }
}
