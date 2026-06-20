<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_type',
        'attendance_id',
        'record_id',
        'user_id',
        'action',
        'old_values',
        'new_values',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
