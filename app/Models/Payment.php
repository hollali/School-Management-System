<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_id',
        'student_id',
        'parent_id',
        'user_id',
        'amount',
        'paid_at',
        'method',
        'reference',
        'transaction_id',
        'channel',
        'payment_type',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parentProfile()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForParent($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
