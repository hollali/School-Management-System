<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Fee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'fee_category_id',
        'invoice_number',
        'amount',
        'paid_amount',
        'balance',
        'due_date',
        'issue_date',
        'academic_term',
        'academic_year',
        'status',
        'payment_status',
        'description',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'balance' => 'decimal:2',
            'due_date' => 'date',
            'issue_date' => 'date',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function feeCategory()
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class, 'fee_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'fee_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'unpaid');
    }

    public function scopePartial($query)
    {
        return $query->where('payment_status', 'partial');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeOverdue($query)
    {
        return $query->whereIn('payment_status', ['unpaid', 'partial'])
            ->where('due_date', '<', now());
    }

    public function scopeForStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeForTerm($query, $term)
    {
        return $query->where('academic_term', $term);
    }

    public function scopeForYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeForClass($query, $classId)
    {
        return $query->whereHas('student', function ($q) use ($classId) {
            $q->whereHas('classes', fn($cq) => $cq->where('classes.id', $classId));
        });
    }
}
