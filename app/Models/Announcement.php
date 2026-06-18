<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'target_role',
        'target_class_id',
        'target_student_id',
        'published_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    public function publisher()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function targetClass()
    {
        return $this->belongsTo(SchoolClass::class, 'target_class_id');
    }

    public function targetStudent()
    {
        return $this->belongsTo(Student::class, 'target_student_id');
    }

    public function scopeForRole($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->whereNull('target_role')
              ->orWhere('target_role', $role);
        });
    }

    public function scopePublished($query)
    {
        return $query->whereNotNull('published_at');
    }

    public function scopeOwnedBy($query, $user)
    {
        return $query->where('published_by', $user->id);
    }
}
