<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admission_number',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function classes()
    {
        return $this->belongsToMany(SchoolClass::class, 'student_class', 'student_id', 'class_id')
            ->withPivot(['assigned_at', 'status'])
            ->withTimestamps();
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class, 'student_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'student_id');
    }

    public function scopeSearchByName($query, $name)
    {
        return $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'like', "%{$name}%");
        });
    }

    public function scopeSearchByEmail($query, $email)
    {
        return $query->whereHas('user', function ($q) use ($email) {
            $q->where('email', 'like', "%{$email}%");
        });
    }

    public function scopeSearchByAdmissionNumber($query, $number)
    {
        return $query->where('admission_number', 'like', "%{$number}%");
    }

    public function scopeByClass($query, $classId)
    {
        return $query->whereHas('classes', function ($q) use ($classId) {
            $q->where('classes.id', $classId);
        });
    }

    public function scopeByParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    public function scopeActive($query)
    {
        return $query->whereHas('classes', function ($q) {
            $q->where('student_class.status', 'active');
        });
    }
}
