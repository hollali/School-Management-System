<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use App\Models\ParentProfile;
use App\Models\Student;
use App\Models\Teacher;

#[Fillable(['name', 'email', 'password', 'profile_photo_path'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentProfile::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function getRoleAttribute(): string
    {
        return $this->roles->first()?->name ?? 'Student';
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isTeacher(): bool
    {
        return $this->hasRole('Teacher');
    }

    public function isStudent(): bool
    {
        return $this->hasRole('Student');
    }

    public function isParent(): bool
    {
        return $this->hasRole('Parent');
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return asset('storage/' . $this->profile_photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=0284c7&background=e0f2fe';
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
